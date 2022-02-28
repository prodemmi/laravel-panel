<?php

namespace Prodemmi\Lava\Http\Controllers;

use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Prodemmi\Lava\Facades\Lava;
use Prodemmi\Lava\Table;

class ResourceController extends Controller
{

    use Table;

    protected $resource, $model, $search, $filter, $sort, $page, $per_page, $records, $env, $last;

    public function table(Request $request)
    {

        $this->env = 'index';

        $this->resource = $request->input( 'resource.resource' );
        $this->model    = $request->input( 'resource.model' );
        $this->page     = $request->input( 'query.page', 1 );
        $this->per_page = $request->input( 'query.per_page', 10 );
        $this->search   = $request->input( 'query.search' );
        $this->sort     = $request->input( 'query.sort' );
        $this->filter   = $request->input( 'query.filter' );

        $resource = $this->resource();

        $this->records = $this->model();

        $this->all = $this->records->count();

        $with = $resource->getWith();

        if ( filled( $with ) ) {

            $this->records->with( $with );

        }

        if ( !( strlen( $this->search ) === 0 ) ) {

            $this->search();

        }

        if ( filled( $this->filter ) ) {

            $this->filter();

        }

        $this->sort();

        return response()->json( $this->sort()->pagination( $this->records->select( $resource->selects() ) ) );
    }

    public function export(Request $request)
    {

        $this->env = 'index';

        $this->resource = $request->input( 'resource.resource' );
        $this->model    = $request->input( 'resource.model' );
        $this->search   = $request->input( 'query.search' );
        $this->sort     = $request->input( 'query.sort' );
        $this->filter   = $request->input( 'query.filter' );
        $columns        = $request->input( 'headers' );
        $selected       = $request->input( 'selected' );

        $resource = $this->resource();
        $selects  = collect( $columns )->pluck( 'column' )->filter( function ($c) use ($resource) {

            return $resource->findField( $c )->inExport();

        } )->toArray();
        $selects = array_intersect( $resource->selects(), $selects );

        $model         = $this->model();
        $this->records = $model->setAppends( [] );

        $result = collect();

        $this->sort();

        if ( filled( $selected ) ) {

            $pk = $resource->getPrimaryKey();

            $result = $this->records->whereIn($pk, array_column(array_column($selected, $pk), 'value'))->get( $selects );

        }
        else {

            if ( filled($this->search) ) {

                $this->search();

            }

            if ( filled( $this->filter ) ) {

                $this->filter();

            }

            $result = $this->records->get( $selects );

        }

        //        remove appends
        $result = $result->map( function ($res) use ($selects) {

            return array_intersect_key( $res->toArray() , array_flip( array_map( 'strval', $selects ) ) );

        } );

        $headers = array_map( function ($s) use ($resource) {

            return $resource->findField( $s )->name ?? '';

        }, $selects );

        return response()->json( [
            'headers' => array_values($headers),
            'data'    => $this->resolveValue( $result, TRUE, FALSE, $resource, TRUE, $selects , false)->map(function($res){

                $newRes = [];
                foreach($res as $key => $r){
                    $newRes [$key] = $r['display'];
                }
                return $newRes;

            })
        ] );

    }

    public function relationTable(Request $request)
    {

        $this->resource   = $request->input( 'resource.resource' );
        $this->model      = $request->input( 'resource.model' );
        $this->search     = $request->input( 'search' );
        $relation         = $request->input( 'relation' );
        $column           = $request->input( 'column' );
        $relationResource = resolve( $request->input( 'relationResource' ) );

        $resource   = $this->resource();
        $primaryKey = $resource->getPrimaryKey();


        $with = $resource->getWith();

        $this->records = $this->model()->with( $with )->where( $primaryKey, $this->search )->get();

        $pluck = $resource->findField( $column )->column;

        switch ( $relation ?? '' ) {
            case 'HasOne':
                $pluck = str_replace( '_id', '', $pluck );
                break;
            case 'belongsTo':
                $pluck = str_replace( '_id', '', $pluck );
                break;
        }

        $records = $this->records->pluck( (string)$pluck )->flatten();

        $this->all = $records->filter()->count();

        return response()->json( [
            'rows'    => $this->all ? $this->resolveValue( $records, TRUE, FALSE, $relationResource ) : [],
            'headers' => $relationResource->headers(),
            'all'     => $this->all
        ] );

    }

    private function saveHasOne($model, $toSave, $primaryKey, $column)
    {

        $model->fill( [
            $column => $toSave->{$primaryKey}
        ] );

        return response()->json( TRUE );

    }

    private function saveHasMany($model, $toSave, $column)
    {
        
        $before = $model->{$column}()->get();

        $toDelete = $before->filter( function ($record) use ($toSave, $model) {

            return !in_array( $record->id, $toSave->pluck( $model->getKeyName() )->toArray() );

        } )->first();

        $model->{$column}()->saveMany( $toSave );
        optional( $toDelete )->delete();

        return response()->json( TRUE );

    }

    private function saveBelongsTo($model, $toSave, $primaryKey, $column){

        $model->fill( [
            $column => $toSave->{$primaryKey}
        ] );

        return response()->json( TRUE );

    }

    private function saveBelongsToMany($model, $toSave, $column){

        $before = $model->{$column}()->get();

        $toDelete = $before->filter( function ($record) use ($toSave, $model) {

            return !in_array( $record->id, $toSave->pluck( $model->getKeyName() )->toArray() );

        } )->first();

        $model->{$column}()->saveMany( $toSave );
        optional( $toDelete )->delete();

        return response()->json( TRUE );

    }

    private function saveMorph($model, $toSave, $column)
    {
        
        $model->{$column}()->sync( $toSave );

        return response()->json( TRUE );

    }

    public function action(Request $request)
    {

        $action         = $request->input( 'action' );
        $values         = $request->input( 'values', [] );
        $this->resource = $request->input( 'resource' );
        $rows           = $request->input( 'rows' );

        $resource = $this->resource();

        if($rows){

            foreach ( $rows as &$row ) {

                $row = $this->removeDisplay( $row );
    
            }

            $rows = collect($rows);

        }else{

            $this->records = $resource->getModelInstance();

            $with = $resource->getWith();

            if ( filled( $with ) ) {

                $this->records = $this->records->with( $with );

            }

            if ( !( strlen( $this->search ) === 0 ) ) {

                $this->search();

            }

            if ( filled( $this->filter ) ) {

                $this->filter();

            }

            $this->sort();

            $rows = $this->records->get();

        }

        $action = resolve( $action );

        $newValue = [];

        foreach ( $values as $value ) {

            $newValue[$value['column']] = $value['value'] ?? null;

        }

        return $action->handle( $rows, $newValue, $resource );

    }

    public function detail(Request $request)
    {

        $this->env = 'detail';

        $this->resource = $request->input( 'resource' );
        $search         = $request->input( 'search' );
        $primaryKey     = $request->input( 'primary_key' );
        $resource       = $this->resource();
        $model          = $resource->getModelInstance();

        $with = $resource->getWith();

        $record = $model->where( $primaryKey, $search );

        if ( filled( $with ) ) {

            $record = $record->with( $with );

        }

        $record = $this->resolveValue( $record->get(), TRUE, FALSE );
        return response()->json( $record->first() );

    }

    public function form(Request $request)
    {

        $this->env = 'edit';

        $this->resource = $request->input( 'resource' );
        $search         = $request->input( 'search' );
        $primaryKey     = $request->input( 'primary_key' );
        $resource       = $this->resource();
        $model          = $resource->getModelInstance();

        $with = $resource->getWith();

        $record = $model->where( $primaryKey, $search );

        if ( filled( $with ) ) {

            $record = $record->with( $with );

        }

        $record = $this->resolveValue( $record->get(), FALSE );

        return response()->json( $record->first() );

    }

    public function selectSearch(Request $request)
    {

        $resource   = resolve($request->input( 'resource' ));
        $search     = $request->input( 'search' );
        $init       = $request->input( 'init', FALSE );
        $subtitle   = $request->input('subtitle');

        $model = $resource->getModelInstance();
        $primaryKey = $resource->getPrimaryKey();

        if ( $init ) {

            $modelKey = $model->getKeyName();

            if ( blank( $search ) ) {

                $options = $model->take( 20 );

            }
            elseif ( is_array( $search ) && filled( $search ) ) {

                $options = $model->whereIn( $modelKey, $search );

            }
            else {

                $options = $model->where( $modelKey, '=', $search );

            }

        }
        else {

            $options = $model->where( $primaryKey, 'like', "%$search%" )
                             ->when( isset( $subtitle ), function ($query, $has) use ($subtitle, $search) {
                                 return $query->orWhere( $subtitle, 'like', "%$search%" );
                             } )
                             ->take( 15 );

        }

        if ( isset( $subtitle ) ) {

            $label    = DB::raw( "CONCAT($primaryKey, ' - ', $subtitle) as label" );

        }
        else {

            $label = "$primaryKey as label";

        }

        if(!$init){
            
            $created_at = Schema::hasColumn($model->getTable(), 'created_at');
            
            $options->latest($created_at ? 'created_at' : $primaryKey);

        }

        return response()->json( $options->get( [
            "$primaryKey as value",
            $label
        ] ) );

    }

    public function getActiveActions(Request $request)
    {

        try{
            $resource   = resolve( $request->input( 'resource' ) );
            $primaryKey = $request->primary_key;
            $search     = $request->search;

            $row = $resource->getModelInstance()->where( $primaryKey, $search )->first();

            $active_actions = collect($resource->getActions())->map(function($action)use($row, $resource){
                
                if(resolve($action['action'])->showOn($row, $resource))
                    return ($action);
                
                return null;

            })->filter();

            return response()->json( $active_actions );
        }catch(Exception $e){
            return response()->json();
        }

    }

    public function update(Request $request)
    {

        $data       = $request->data ?? [];
        $primaryKey = $request->primary_key;
        $search     = $request->search;
        $resource   = resolve( $request->resource );
        $rules      = $resource->getRules();
        
        $noRelations = [];
        $relations = [];

        collect($data)->each(function($d)use(&$noRelations, &$relations){

            if(isset($d['relationType']))
                $relations[$d['column']] = $d['value'] ?? null;
            else
                $noRelations[$d['column']] = $d['value'] ?? null;

        });

        $newData = array_merge($noRelations, $relations);

        $validator = Validator::make( $newData, array_intersect_key( $rules, array_flip(array_keys($newData)) ) );

        if ( $validator->fails() ) {
            return response()->json( [
                'errors' => $validator->errors()
            ], 422 );
        }

        try {

            $model = $resource->getModelInstance()->where( $primaryKey, $search )->first();

            if(filled($noRelations)){

                $model->update($noRelations);

            }
            
            foreach ($relations ?? [] as $column => $value) {

                $relation = collect($data)->first(function($d) use ($column){


                    return $d['column'] == $column;

                });

                $relationModel = $relation['relationModel'] ?? null;

                $relationType = $relation['relationType'] ?? null;

                if ( blank( $value ) ) {

                    $value = NULL;
        
                }

                $newColumn = collect($resource->getWith())->first(function($with)use($column){
                    return str_contains($column, $with);
                });
             
                // $column = $newColumn ?: $column;
                
                if($relationModel){
                    // dump($relationModel);

                    $relationModel = resolve( $relationModel );

                    $function = is_array($value) ? 'whereIn' : 'where';
                    $get = is_array($value) ? 'get' : 'first';

                    $key = $relation['relationPrimaryKey'] ?? null ? $relation['relationPrimaryKey'] : $relationModel->getKeyName();
                    // dump($column, $key, $value, '---');

                    $toSave = $relationModel->{$function}( $key, $value )->{$get}();

                }

                switch ($relationType) {
                    case 'HasOne':
                        $this->saveHasOne($model, $toSave, $relationModel->getKeyName(), $column);
                        break;
                    case 'HasMany':
                        $this->saveHasMany($model, $toSave, $column);
                        break;
                    case 'BelongsTo':
                        $this->saveBelongsTo($model, $toSave, $relationModel->getKeyName(), $column);
                        break;
                    case 'BelongsToMany':
                        $this->saveBelongsToMany($model, $toSave, $column);
                        break;
                    case 'MorphToMany':
                        $this->saveMorph($model, $toSave, $column);
                        break;
                    case 'MorphedByMany':
                        $this->saveMorph($model, $toSave, $column);
                        break;
                }
                
            }

            $model->save();

            return response()->json( [ 'message' => "Update successfully." ] );

        }
        catch ( \Exception $e ) {

            return response()->json( [ 
                'message' => $e->getMessage() ,
                'file'    => $e->getFile(),
                'line'    => $e->getLine()
            ], 422 );

        }

    }

    public function store(Request $request)
    {

        $data     = $request->data ?? [];
        $resource = resolve( $request->input( 'resource' ) );

        $rules    = $resource->getRules();

        $noRelations = [];
        $relations = [];

        collect($data)->each(function($d)use(&$noRelations, &$relations){

            if(isset($d['relationType']))
                $relations[$d['column']] = $d['value'] ?? null;
            else
                $noRelations[$d['column']] = $d['value'] ?? null;

        });

        $newData = array_merge($noRelations, $relations);

        $validator = Validator::make( $newData, $rules );

        if ( $validator->fails() ) {
            return response()->json( [
                'errors' => $validator->errors()
            ], 422 );
        }

        try {

            $model = $resource->getModelInstance()->fill($noRelations);

            foreach ($relations ?? [] as $column => $value) {

                $relation = collect($data)->first(function($d) use ($column){

                    return $d['column'] == $column;

                });

                $relationModel = $relation['relationModel'] ?? null;

                $relationType = $relation['relationType'] ?? null;

                if ( blank( $value ) ) {

                    $value = NULL;
        
                }
                
                if($relationModel){

                    $relationModel = resolve( $relationModel );

                    $function = is_array($value) ? 'whereIn' : 'where';
                    $get = is_array($value) ? 'get' : 'first';

                    $key = $relation['relationPrimaryKey'] ?? null ? $relation['relationPrimaryKey'] : $relationModel->getKeyName();

                    $toSave = $relationModel->{$function}( $key, $value )->{$get}();

                }

                switch ($relationType) {
                    case 'HasOne':
                        $this->saveHasOne($model, $toSave, $relationModel->getKeyName(), $column);
                        break;
                    case 'BelongsTo':
                        $this->saveBelongsTo($model, $toSave, $relationModel->getKeyName(), $column);
                        break;

                }
                
            }

            $model = $model->save();

            foreach ($relations ?? [] as $column => $value) {

                $relation = collect($data)->first(function($d) use ($column){


                    return $d['column'] == $column;

                });

                $relationModel = $relation['relationModel'] ?? null;

                $relationType = $relation['relationType'] ?? null;

                if ( blank( $value ) ) {

                    $value = NULL;
        
                }

                $newColumn = collect($resource->getWith())->first(function($with)use($column){
                    return str_contains($column, $with);
                });
             
                // $column = $newColumn ?: $column;
                
                if($relationModel){

                    $relationModel = resolve( $relationModel );

                    $function = is_array($value) ? 'whereIn' : 'where';
                    $get = is_array($value) ? 'get' : 'first';

                    $key = $relation['relationPrimaryKey'] ?? null ? $relation['relationPrimaryKey'] : $relationModel->getKeyName();

                    $toSave = $relationModel->{$function}( $key, $value )->{$get}();

                }

                switch ($relationType) {
                    case 'hasMany':
                        $this->saveHasMany($model, $toSave, $column);
                        break;
                    case 'belongsToMany':
                        $this->saveBelongsToMany($model, $toSave, $column);
                        break;
                    case 'morphToMany':
                        $this->saveMorph($model, $toSave, $column);
                        break;
                    case 'morphedByMany':
                        $this->saveMorph($model, $toSave, $column);
                        break;

                }
                
            }

            return response()->json( [ 'message' => "Store successfully." ] );

        }
        catch ( \Exception $e ) {

            return response()->json( [ 
                'message' => $e->getMessage() ,
                'file'    => $e->getFile(),
                'line'    => $e->getLine()
            ], 422 );

        }

    }

    public function storeFilter(Request $request)
    {

        $resource   = $request->input( 'resource' );
        $title      = $request->input( 'title' );
        $filters    = $request->input( 'filters' );
        $edit_model = $request->input( 'edit' );
        $id         = $request->input( 'id' );

        try {

            if ( $edit_model ) {

                DB::table( 'lava_filters' )->where( 'id', $id )->update( [
                    'title'  => $title,
                    'filters' => json_encode( $filters )
                ] );

            }
            else {

                DB::table( 'lava_filters' )->insert( [
                    'title'    => $title,
                    'filters'   => json_encode( $filters ),
                    'resource' => $resource
                ] );

            }

            return response()->json( [
                'message' => "Filter " . strtolower( $title ) . " successfully " . ( $edit_model ? 'edited.' : 'created.' ),
                'result'  => TRUE
            ] );

        }
        catch ( \Exception $e ) {

            return response()->json( [ 'message' => $e->getMessage() ], 422 );

        }

    }

    public function deleteFilter(Request $request)
    {

        $id    = $request->input( 'filter_id' );
        $title = $request->input( 'title' );

        try {

            $filter = DB::table( 'lava_filters' )->where( 'id', $id )->delete();

            return response()->json( [
                'message' => "Filter " . strtolower( $title ) . " successfully deleted.",
                'result'  => TRUE
            ] );

        }
        catch ( \Exception $e ) {

            return response()->json( [ 'message' => $e->getMessage() ], 422 );

        }

    }

    public function getConfig(){

        return response()->json(Lava::getActivePanel()->getConfig());

    }

    public function checkLicense(Request $request){

        $hasKey = $request->input('key') ===  '12345';
        $hasUsername = $request->input('username') === 'prodemmi';
        $hasPassword = $request->input('password') ===  '54879asd2534asd';

        return response()->json($hasKey && $hasUsername && $hasPassword);

    }

    public function getLastCounts(Request $request){

        $data = $request->input('last_count', []);

        return collect( Lava::getActivePanel()->getResources() )->where('tool', false)->map(function($resource) use ($data){
            
            $resource = resolve($resource['resource']);

            $l = collect($data)->filter()->first(function($d) use ($resource){

                return ($d['resource'] ?? null) === get_class($resource);

            });
            
            if($l && $l['last']){

                $new_count = resolve($l['resource'])->getModelInstance()->where($l['last_key'], '>', $l['last'])->count();
                $l['new_count'] = $new_count;

                return $l;

            }

            return $this->getLast($resource);

        });

    }

}
