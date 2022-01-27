<?php

namespace Prodemmi\Lava\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use function PHPSTORM_META\type;
use Prodemmi\Lava\Table;

class ResourceController extends Controller
{

    use Table;

    protected $resource, $model, $search, $filter, $sort, $page, $per_page, $records, $env;

    public function __construct()
    {
        $this->middleware( 'api' );
    }

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

        if ( !empty( $with ) ) {

            //            $this->records->with( $with );

        }

        if ( !( strlen( $this->search ) === 0 ) ) {

            $this->search();

        }

        if ( !empty( $this->filter ) ) {

            $this->filter();

        }

        $this->sort();

        return response()->json( $this->sort()->pagination( $this->records ) );
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

        $action = new( $action )();

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

        $record = $model->where( $primaryKey, $search )->get( $resource->selects() );

        $record = $this->resolveValue( $record, TRUE, FALSE );

        return response()->json( $record->first() );

    }

    public function form(Request $request)
    {

        $this->env = 'edit';

        $this->resource = $request->input( 'resource' );
        $search         = $request->input( 'search' );
        $primaryKey     = $request->input( 'primary_key' );

        $model = $this->resource()::getModelInstance();

        $record = $model->where( $primaryKey, $search )->get();

        $record = $this->resolveValue( $record, FALSE );

        return response()->json( $record->first() );


    }

    public function select(Request $request)
    {

        $resource = new ( $request->input( 'resource' ) )();

        $field = $resource->findField( $request->input( 'field' ) );
        $init  = $request->input( 'init', FALSE );

        $options = call_user_func( $field->searchCallback, $request->input( 'search' ), $init );

        return response()->json( $options );


    }

    public function update(Request $request)
    {

        $data       = $request->data ?? [];
        $primaryKey = $request->primary_key;
        $search     = $request->search;
        $resource   = new ( $request->input( 'resource' ) )();
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
        $resource = new ( $request->input( 'resource' ) )();
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

        $resource = $request->input( 'resource' );
        $title    = $request->input( 'title' );
        $filters  = $request->input( 'filters' );

        try {

            DB::table( 'lava_filters' )->insert( [
                'title'    => $title,
                'filter'   => json_encode( $filters ),
                'resource' => $resource
            ] );

            return response()->json( [
                'message' => "Filter " . strtolower( $title ) . " successfully created.",
                'result'  => TRUE
            ] );

        }
        catch ( \Exception $e ) {

            return response()->json( [ 'message' => $e->getMessage() ], 422 );

        }

    }

    public function deleteFilter(Request $request)
    {

        $id = $request->input( 'filter_id' );
        $title = $request->input( 'title' );

        try {

            $filter = DB::table( 'lava_filters' )->where('id', $id )->delete();

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
