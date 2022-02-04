<?php

namespace Prodemmi\Lava\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use function PHPSTORM_META\type;
use Prodemmi\Lava\Table;
use Ramsey\Uuid\Builder\BuilderCollection;

class ResourceController extends Controller
{

    use Table;

    protected $resource, $model, $search, $filter, $sort, $page, $per_page, $records, $env;

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

        $this->records = $this->model()->select( $resource->selects() );

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

        return response()->json( $this->sort()->pagination( $this->records ) );
    }

    public function relation(Request $request)
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
            case 'hasOne':
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

    public function updateHasOne(Request $request)
    {

        $resource      = $request->input( 'resource' );
        $search        = $request->input( 'search' );
        $update_column = $request->input( 'update_column' );
        $value         = $request->input( 'value' );

        if ( blank( $value ) ) {

            $value = NULL;

        }

        resolve( $resource['model'] )->where( $resource['primaryKey'], $search )->update( [
            $update_column => $value
        ] );

        return response()->json( TRUE );

    }

    public function updateHasMany(Request $request)
    {

        $model         = $request->input( 'model' );
        $primaryKey    = $request->input( 'primaryKey' );
        $search        = $request->input( 'search' );
        $relation      = $request->input( 'relation' );
        $values        = $request->input( 'values' );
        $relationModel = $request->input( 'relationModel' );

        $model = resolve( $model )->where( $primaryKey, $search )->first();

        $relationModel = resolve( $relationModel );
        $toSave        = $relationModel->whereIn( $relationModel->getKeyName(), $values )->get();

        $before = $model->{$relation}()->get();

        $toDelete = $before->filter( function ($record) use ($toSave) {

            return !in_array( $record->id, $toSave->pluck( 'id' )->toArray() );

        } )->first();


        $model->{$relation}()->saveMany( $toSave );
        optional( $toDelete )->delete();

        return response()->json( TRUE );

    }

    public function updateMorph(Request $request)
    {

        $model         = $request->input( 'model' );
        $primaryKey    = $request->input( 'primaryKey' );
        $search        = $request->input( 'search' );
        $relation      = $request->input( 'relation' );
        $values        = $request->input( 'values' );
        $relationModel = $request->input( 'relationModel' );


        $relationModel = resolve( $relationModel );
        $toSave        = $relationModel->whereIn( $relationModel->getKeyName(), $values )->get();

        $module = resolve( $model )->where( $primaryKey, $search )->first();
        $module->{$relation}()->sync( $toSave );

        return response()->json( TRUE );

    }

    public function action(Request $request)
    {

        $action         = $request->input( 'action' );
        $values         = $request->input( 'values', [] );
        $this->resource = $request->input( 'resource' );
        $rows           = $request->input( 'rows' );

        foreach ( $rows as &$row ) {

            $row = $this->removeDisplay( $row );

        }

        $action = resolve( $action );

        $newValue = [];
        foreach ( $values as $value ) {

            $newValue[$value['column']] = $value['value'];

        }

        return $action->handle( collect( $rows ), $newValue, $this->resource() );

    }

    public function detail(Request $request)
    {

        $this->env = 'detail';

        $this->resource = $request->input( 'resource' );
        $search         = $request->input( 'search' );
        $primaryKey     = $request->input( 'primary_key' );

        $resource = $this->resource();

        $model = $resource::getModelInstance();

        $record = $model->where( $primaryKey, $search );

        $with = $resource->getWith();

        if ( filled( $with ) ) {

            $record = $record->with( $with );

        }

        $record = $record->get( $resource->selects() );

        $record = $this->resolveValue( $record, TRUE, FALSE );

        return response()->json( $record->first() );

    }

    public function form(Request $request)
    {

        $this->env = 'edit';

        $this->resource = $request->input( 'resource' );
        $search         = $request->input( 'search' );
        $primaryKey     = $request->input( 'primary_key' );
        $resource       = $this->resource();
        $model          = $resource::getModelInstance();

        $with = $resource->getWith();

        $record = $model->where( $primaryKey, $search );

        if ( filled( $with ) ) {

            $record = $record->with( $with );

        }

        $record = $this->resolveValue( $record->get(), FALSE );

        return response()->json( $record->first() );


    }

    public function select(Request $request)
    {

        $resource = resolve( $request->input( 'resource' ) );

        $field = $resource->findField( $request->input( 'field' ) );
        $init  = $request->input( 'init', FALSE );

        $options = call_user_func( $field->searchCallback, $request->input( 'search' ), $init );

        return response()->json( $options );

    }

    public function searchSelect(Request $request)
    {

        $resource   = $request->input( 'resource' );
        $search     = $request->input( 'search' );
        $init       = $request->input( 'init', FALSE );
        $primaryKey = $resource['primaryKey'];

        $model = resolve( $resource['model'] );

        $modelKey = $model->getKeyName();

        if ( $init ) {

            if ( blank( $search ) ) {

                $options = $model->take(20)->orderByDesc($modelKey);

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
                             ->when( isset( $resource['subtitle'] ), function ($query, $has) use ($resource, $search) {
                                 return $query->orWhere( $resource['subtitle'], 'like', "%$search%" );
                             } )
                             ->take( 15 );

        }

        if ( isset( $resource['subtitle'] ) ) {

            $subtitle = $resource['subtitle'];
            $label    = DB::raw( "CONCAT($primaryKey, ' - ', $subtitle) as label" );

        }
        else {

            $label = "$primaryKey as label";

        }

        return response()->json( $options->get( [
            "$modelKey as value",
            $label
        ] ) );


    }

    public function update(Request $request)
    {

        $data       = $request->data ?? [];
        $primaryKey = $request->primary_key;
        $search     = $request->search;
        $resource   = resolve( $request->input( 'resource' ) );
        $rules      = $resource->getRules();

        $validator = Validator::make( $data, array_intersect_key( $rules, $data ) );

        if ( $validator->fails() ) {
            return response()->json( [
                'errors' => $validator->errors()
            ], 422 );
        }

        try {

            $model = $resource::getModelInstance();

            $model->where( $primaryKey, $search )->first()->update( $data );

            return response()->json( [ 'message' => "Update successfully." ] );

        }
        catch ( \Exception $e ) {

            return response()->json( [ 'message' => $e->getMessage() ], 422 );

        }

    }

    public function store(Request $request)
    {

        $data     = $request->data ?? [];
        $resource = resolve( $request->input( 'resource' ) );
        $rules    = $resource->getRules();

        $validator = Validator::make( $data, array_filter( $rules ) );

        if ( $validator->fails() ) {
            return response()->json( [
                'errors' => $validator->errors()
            ], 422 );
        }

        try {

            $model = $resource::getModelInstance();

            $model->create( $data );

            return response()->json( [ 'message' => "Store successfully." ] );

        }
        catch ( \Exception $e ) {

            return response()->json( [ 'message' => $e->getMessage() ], 422 );

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
                    'filter' => json_encode( $filters )
                ] );

            }
            else {

                DB::table( 'lava_filters' )->insert( [
                    'title'    => $title,
                    'filter'   => json_encode( $filters ),
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

}
