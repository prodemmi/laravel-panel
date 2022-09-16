<?php

namespace Prodemmi\Lava\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Prodemmi\Lava\Facades\Lava;
use Prodemmi\Lava\Fields\Slug;
use Prodemmi\Lava\Table;

class ResourceController extends Controller
{

    use Table;

    protected $resource, $model, $search, $filter, $limit, $sort, $page, $per_page, $records, $env, $last;

    public function table(Request $request)
    {

        $this->env = 'index';

        $this->resource = $request->input('resource.resource');
        $this->model    = $request->input('resource.model');
        $this->page     = $request->input('query.page', 1);
        $this->per_page = $request->input('query.per_page', 10);
        $this->search   = $request->input('query.search');
        $this->sort     = $request->input('query.sort');
        $this->filter   = $request->input('query.filter');
        $this->limit    = $request->input('query.limit');

        $limitKey    = basename(str_replace('\\', '/', $this->resource));
        $limitRecord = DB::table('lava_options')->where('key', "$limitKey.limit");
        $lastLimit   = $limitRecord->first()?->value;

        if (!$this->limit){

            $limitRecord = $limitRecord->orderBy('id')->limit(1);
            $limitRecord->delete();

        }elseif($this->limit !== $lastLimit){

            if($limitRecord->exists()){
                $limitRecord->update([
                    'key'   => "$limitKey.limit",
                    'value' => $this->limit
                ]);
            }else{
                $limitRecord->insert([
                    'key'   => "$limitKey.limit",
                    'value' => $this->limit
                ]);
            }

        }

        $resource = $this->resource();

        $this->records = $this->model();

        $this->all = $this->records->count();

        $with = $resource->getWith();

        if (filled($with)) {

            $this->records->with($with);
        }

        if (!(strlen($this->search) === 0)) {

            $this->search();
        }

        if (filled($this->filter)) {

            $this->filter();
        }

        $this->total = $this->records->count();

        return response()->json($this->sort()->pagination($this->records->select($resource->selects())));
    }

    public function export(Request $request)
    {

        $this->env = 'index';

        $this->resource = $request->input('resource.resource');
        $this->model    = $request->input('resource.model');
        $this->search   = $request->input('query.search');
        $this->sort     = $request->input('query.sort');
        $this->filter   = $request->input('query.filter');
        $columns        = $request->input('headers');
        $selected       = $request->input('selected');

        $resource = $this->resource();
        $selects  = collect($columns)->pluck('column')->filter(function ($c) use ($resource) {

            return $resource->findField($c)->inExport();
        })->toArray();
        $selects = array_intersect($resource->selects(), $selects);

        $model         = $this->model();
        $this->records = $model->setAppends([]);

        $result = collect();

        $this->sort();

        if (filled($selected)) {

            $pk = $resource->getPrimaryKey();

            $result = $this->records->whereIn($pk, array_column(array_column($selected, $pk), 'value'))->get($selects);
        } else {

            if (filled($this->search)) {

                $this->search();
            }

            if (filled($this->filter)) {

                $this->filter();
            }

            $result = $this->records->get($selects);
        }

        //        remove appends
        $result = $result->map(function ($res) use ($selects) {

            return array_intersect_key($res->toArray(), array_flip(array_map('strval', $selects)));
        });

        $headers = array_map(function ($s) use ($resource) {

            return $resource->findField($s)->name ?? '';
        }, $selects);

        return response()->json([
            'headers' => array_values($headers),
            'data'    => $this->resolveValue($result, TRUE, FALSE, $resource, TRUE, $selects, false)->map(function ($res) {

                $newRes = [];
                foreach ($res as $key => $r) {
                    $newRes[$key] = $r['display'];
                }
                return $newRes;
            })
        ]);
    }

    public function relationTable(Request $request)
    {

        $this->resource   = $request->input('resource.resource');
        $this->model      = $request->input('resource.model');
        $this->search     = $request->input('search');
        $relation         = $request->input('relation');
        $column           = $request->input('column');
        $relationResource = resolve($request->input('relationResource'));

        $resource   = $this->resource();
        $primaryKey = $resource->getPrimaryKey();


        $with = $resource->getWith();

        $this->records = $this->model()->with($with)->where($primaryKey, $this->search)->get();

        $pluck = $resource->findField($column)->column;

        switch ($relation ?? '') {
            case 'HasOne':
                $pluck = str_replace('_id', '', $pluck);
                break;
            case 'belongsTo':
                $pluck = str_replace('_id', '', $pluck);
                break;
        }

        $records = $this->records->pluck((string)$pluck)->flatten();

        $this->all = $records->filter()->count();

        return response()->json([
            'rows'    => $this->all ? $this->resolveValue($records, TRUE, FALSE, $relationResource) : [],
            'headers' => $relationResource->headers(),
            'all'     => $this->all
        ]);
    }

    private function saveHasOne($model, $toSave, $primaryKey, $update_column)
    {

        $model->fill([
            $update_column => $toSave->{$primaryKey}
        ]);

        return response()->json(TRUE);
    }

    private function saveHasMany($model, $toSave, $column)
    {

        $before = $model->{$column}()->get();

        $toDelete = $before->filter(function ($record) use ($toSave, $model) {

            return !in_array($record->id, $toSave->pluck($model->getKeyName())->toArray());
        })->first();

        $model->{$column}()->saveMany($toSave);
        optional($toDelete)->delete();

        return response()->json(TRUE);
    }

    private function saveBelongsTo($model, $toSave, $primaryKey, $update_column)
    {

        $model->fill([
            $update_column => $toSave->{$primaryKey}
        ]);

        return response()->json(TRUE);
    }

    private function saveBelongsToMany($model, $toSave, $column)
    {

        $before = $model->{$column}()->get();

        $toDelete = $before->filter(function ($record) use ($toSave, $model) {

            return !in_array($record->id, $toSave->pluck($model->getKeyName())->toArray());
        })->first();

        $model->{$column}()->saveMany($toSave);
        optional($toDelete)->delete();

        return response()->json(TRUE);
    }

    private function saveMorph($model, $toSave, $column)
    {

        $model->{$column}()->sync($toSave);

        return response()->json(TRUE);
    }

    public function action(Request $request)
    {

        $action         = $request->input('action.class');
        $values         = $request->input('values', []);
        $resource       = resolve($request->input('action.resource'));
        $rows           = $request->input('rows');

        if ($rows) {

            foreach ($rows as &$row) {

                $row = $this->mapValue($row);
            }

        } else {

            $this->records = $resource->getModelInstance();

            $with = $resource->getWith();

            if (filled($with)) {

                $this->records = $this->records->with($with);
            }

            if (!(strlen($this->search) === 0)) {

                $this->search();
            }

            if (filled($this->filter)) {

                $this->filter();
            }

            $rows = $this->records->get()->toArray();
        }

        $newValues = [];

        foreach ($values as $value) {

            $newValues[$value['column']] = $value['value'] ?? null;
        }

        return resolve($action)->handle(collect($rows), $newValues, $resource);
    }

    public function detail(Request $request)
    {

        $this->env = __FUNCTION__;

        $this->resource = $request->input('resource');
        $search         = urldecode($request->input('search'));
        $primaryKey     = $request->input('primary_key');
        $resource       = $this->resource();
        $model          = $resource->getModelInstance();

        $with = $resource->getWith();

        $record = $model->where($primaryKey, $search);

        if (filled($with)) {
            $record = $record->with($with);
        }

        $record = $this->resolveValue($record->get());

        return response()->json($record->first());
    }

    public function form(Request $request)
    {

        $this->env = 'edit';

        $this->resource = $request->input('resource');
        $search         = urldecode($request->input('search'));
        $primaryKey     = $request->input('primary_key');
        $resource       = $this->resource();
        $model          = $resource->getModelInstance();

        $with = $resource->getWith();

        $record = $model->where($primaryKey, $search);

        if (filled($with)) {

            $record = $record->with($with);
        }

        $record = $this->resolveValue($record->get(), FALSE, TRUE, $resource, FALSE, [], FALSE);

        return response()->json($record->first());
    }

    public function selectSearch(Request $request)
    {

        $resource   = resolve($request->input('resource'));
        $search     = urldecode($request->input('search'));
        $init       = $request->input('init', FALSE);
        $subtitle   = $request->input('subtitle');

        $model = $resource->getModelInstance();
        $primaryKey = $resource->getPrimaryKey();

        if ($init) {

            $modelKey = $model->getKeyName();

            if (blank($search)) {

                $options = $model->take(20);
            } elseif (is_array($search) && filled($search)) {

                $options = $model->whereIn($modelKey, $search);
            } else {

                $options = $model->where($modelKey, '=', $search);
            }
        } else {

            $options = $model->where($primaryKey, 'like', "%$search%")
                ->when(isset($subtitle), function ($query, $has) use ($subtitle, $search) {
                    return $query->orWhere($subtitle, 'like', "%$search%");
                })
                ->take(15);

        }

        if (isset($subtitle)) {

            $label    = DB::raw("CONCAT($primaryKey, ' - ', $subtitle) as label");

        } else {

            $label = "$primaryKey as label";

        }

        if (!$init) {

            $created_at = Schema::hasColumn($model->getTable(), 'created_at');

            $options->latest($created_at ? 'created_at' : $primaryKey);

        }

        return response()->json($options->get([
            "$primaryKey as value",
            $label
        ]));

    }

    public function searchableSelect(Request $request)
    {

        $resource   = resolve($request->input('resource'));
        $field      = explode('__', $request->input('field'))[0];
        $search     = $request->input('search');

        $field = $resource->findField($field);

        return response()->json(call_user_func($field->searchCallback, $search));
    }

    public function getActiveActions(Request $request)
    {

        try {
            $resource   = resolve($request->input('resource'));
            $primaryKey = $request->primary_key;
            $search     = urldecode($request->search);

            $row = $resource->getModelInstance()->where($primaryKey, $search)->first();

            $active_actions = collect($resource->getActions())->map(function ($action) use ($row, $resource) {

                if (resolve($action['class'])->showOn(optional($row)->toArray(), $resource))
                    return ($action);

                return null;
            })->filter();

            return response()->json($active_actions);
        } catch (Exception $e) {
            return response()->json();
        }
    }

    public function update(Request $request)
    {

        $data       = $request->data ?? [];
        $primaryKey = $request->primary_key;
        $search     = $request->search;
        $resource   = resolve($request->resource);
        $rules      = $resource->getRules();

        $newData = [];

        collect($data)->each(function ($d) use (&$newData) {

            if(!($d['file'] ?? true))
                $newData[$d['column']] = $d['value'] ?? null;

        });

        $validator = Validator::make($newData, array_intersect_key($rules, array_flip(array_keys($newData))));

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            $model = $resource->getModelInstance()->where($primaryKey, $search)->first();

            // File field on upload event
            foreach (array_filter($data, fn ($d) => isset($d['file']) && $d['multiple']) as $row) {

                $field = $resource->findField($row['column']);
                if($row['file'] ?? false){
                    $v = $row['value'] ?? [];
                    if(isset($row['all']) && !empty($row['all']) && empty($row['value']) && isset($field->onDelete)){
                        call_user_func($field->onDelete, $field->maxFiles > 1 ? $v : Arr::first($v), $model);
                    }elseif(isset($field->onUpdate) && $field->onUpdate){
                        call_user_func($field->onUpdate, $field->maxFiles > 1 ? $v : Arr::first($v), $model);
                    }

                }

            }

            $toUpdate = [];
            foreach (array_filter($data, fn ($d) => (isset($d['update_column']) && isset($d['update_column'])) || (isset($d['file']) && $d['file'] && !$d['multiple']) || !isset($d['update_column'])) as $row) {

                $field = $resource->findField($row['column']);

                if($row['file'] ?? false){

                    if(isset($row['all']) && !empty($row['all']) && empty($row['value']) && isset($field->onDelete)){
                        call_user_func($field->onDelete, $model);
                    }elseif(isset($field->onUpdate) && $field->onUpdate){
                        call_user_func($field->onUpdate, $model, $row['value'] ?? null);
                    }

                }

                if (isset($row['relationModel'])) {

                    $toSave = $this->toSave($row);
                    $key = resolve($row['relationModel'])->getKeyName();
                    $toUpdate[$row['update_column']] = $toSave->{$key};
                    
                } else {

                    $toUpdate[$row['column']] = $row['value'];
                    
                }
            }

            $slug = $resource->getSlugField();

            if($slug && isset($toUpdate[$slug->createFrom])){

                $toUpdate[(string)$slug->column] = Slug::createSlug($toUpdate[$slug->createFrom]);

            }

            $model->update($toUpdate);

            foreach (array_filter($data, fn ($d) => isset($d['relationType']) && empty($d['update_column'])) as $row) {

                $toSave = $this->toSave($row);
                $relation = $row['column'];

                $function = $model->{$relation}();
                $method = 'save';

                if (method_exists($function, 'saveMany')) {
                    $method = 'saveMany';
                    $before = $model->{$relation}()->get();
                    $toDelete = $before->filter(function ($record) use ($toSave, $model) {
                        return !in_array($record->id, $toSave->pluck($model->getKeyName())->toArray());
                    })->first();
                    optional($toDelete)->delete();
                } else {
                    $method = 'sync';
                }

                $model->{$relation}()->{$method}($toSave);
            }

            return response()->json(['message' => "Update successfully."]);
        } catch (\Exception $e) {

            return response()->json([
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine()
            ], 422);
        }
    }

    public function store(Request $request)
    {

        $data     = $request->data ?? [];
        $resource = resolve($request->input('resource'));
        $rules    = $resource->getRules();

        $newData = [];

        collect($data)->each(function ($d) use (&$newData) {

            if(!($d['file'] ?? true))
                $newData[$d['column']] = $d['value'] ?? null;
            
        });

        $validator = Validator::make($newData, $rules);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            $model = $resource->getModelInstance();

            $toUpdate = [];
            foreach (array_filter($data, fn ($d) => (isset($d['update_column']) && ($d['update_column'])) || !isset($d['relationType'])) as $row) {
                
                if (isset($row['relationModel'])) {

                    $toSave = $this->toSave($row);
                    $key = resolve($row['relationModel'])->getKeyName();
                    $toUpdate[$row['update_column']] = $toSave->{$key};

                } else {

                    $toUpdate[$row['column']] = $row['value'];

                }
            }

            $slug = $resource->getSlugField();

            if($slug && isset($toUpdate[$slug->createFrom])){

                $toUpdate[(string)$slug->column] = Slug::createSlug($toUpdate[$slug->createFrom]);

            }

            $model = $model->create($toUpdate);

            foreach (array_filter($data, fn ($d) => isset($d['relationType']) && empty($d['update_column'])) as $row) {

                $toSave = $this->toSave($row);
                $relation = $row['column'];

                $function = $model->{$relation}();
                $method = 'save';

                if (method_exists($function, 'saveMany')) {
                    $method = 'saveMany';
                } else {
                    $method = 'sync';
                }

                $model->{$relation}()->{$method}($toSave);
            }

            foreach(array_filter($data, fn ($d) => isset($d['file']) && $d['file']) as $file){

                $field = $resource->findField($file['column']);
                if(isset($field->onUpdate) && $field->onUpdate){

                    call_user_func($field->onUpdate, $model, $file['value'] ?? null);

                }

            }

            return response()->json(['message' => "Store successfully.", 'data' => $model]);


        } catch (\Exception $e) {

            return response()->json([
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine()
            ], 422);
        }
    }

    protected function toSave($row)
    {

        $relationModel = $row['relationModel'] ?? null;
        $relationPrimaryKey = $row['relationPrimaryKey'] ?? null;
        $value = $row['value'];

        if (blank($value)) {

            $value = NULL;

        }

        $relationModel = resolve($relationModel);

        $function = is_array($value) ? 'whereIn' : 'where';
        $get = is_array($value) ? 'get' : 'first';

        $key = $relationPrimaryKey ?? null ? $relationPrimaryKey : $relationModel->getKeyName();

        return $relationModel->{$function}($key, $value)->{$get}();
    }

    public function storeFilter(Request $request)
    {

        $resource   = $request->input('resource');
        $title      = $request->input('title');
        $filters    = Arr::only($request->input('filters'), ['con', 'name', 'value', 'where', 'column', 'component']);
        $edit_model = $request->input('edit');
        $id         = $request->input('id');

        try {

            if ($edit_model) {

                DB::table('lava_filters')->where('id', $id)->update([
                    'title'  => $title,
                    'filters' => json_encode($filters)
                ]);
            } else {

                DB::table('lava_filters')->insert([
                    'title'    => $title,
                    'filters'   => json_encode($filters),
                    'resource' => $resource
                ]);
            }

            return response()->json([
                'message' => "Filter " . strtolower($title) . " successfully " . ($edit_model ? 'edited.' : 'created.'),
                'result'  => TRUE
            ]);
        } catch (\Exception $e) {

            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function deleteFilter(Request $request)
    {

        $id    = $request->input('filter_id');
        $title = $request->input('title');

        try {

            $filter = DB::table('lava_filters')->where('id', $id)->delete();

            return response()->json([
                'message' => "Filter " . strtolower($title) . " successfully deleted.",
                'result'  => TRUE
            ]);
        } catch (\Exception $e) {

            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function getConfig()
    {

        return response()->json(Lava::getActivePanel()->getConfig());
    }

    public function checkLicense(Request $request)
    {

        $hasKey = $request->input('key') ===  '12345';
        $hasUsername = $request->input('username') === 'prodemmi';
        $hasPassword = $request->input('password') ===  '54879asd2534asd';

        return response()->json($hasKey && $hasUsername && $hasPassword);
    }

}
