<?php

namespace Prodemmi\Lava\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Prodemmi\Lava\Table;

class ResourceController extends Controller
{

    use Table;

    protected $resource, $model, $search, $filter, $sort, $page, $per_page, $records;

    public function __construct()
    {
        $this->middleware( 'api' );
    }

    public function table(Request $request)
    {

        $this->resource = $request->input( 'resource.resource' );
        $this->model    = $request->input( 'resource.model' );
        $this->page     = $request->input( 'query.page', 1 );
        $this->per_page = $request->input( 'query.per_page', 10 );
        $this->search   = $request->input( 'query.search' );
        $this->sort     = $request->input( 'query.sort' );
        $this->filter   = $request->input( 'query.filter' );

        $resource = $this->resource();

        $this->records = $this->model()->select( $resource->selects() );

        $with = $resource->getWith();

        if ( !empty( $with ) ) {

            //            $this->records->with( $with );

        }

        if ( !( strlen( $this->search ) === 0 ) ) {

            $this->search();

        }

        if ( $this->filter ) {

            $this->filter();

        }

        $this->sort();

        return response()->json( $this->pagination( $this->records ) );
    }

    public function action(Request $request)
    {

        $action         = $request->input( 'action' );
        $fields         = $request->input( 'fields', [] );
        $this->resource = $request->input( 'resource' );
        $rows           = $request->input( 'rows' );

        foreach ( $rows as &$row ) {

            $row = $this->removeDisplay( $row );

        }

        $action = new( $action )();

        return $action->handle( collect( $rows ), $fields, $this->resource() );

    }

    public function detail(Request $request)
    {

        $this->resource = $request->input( 'resource' );
        $search         = $request->input( 'search' );
        $primaryKey     = $request->input( 'primary_key' );

        $model = $this->resource()::getModelInstance();

        $record = $model->where( $primaryKey, $search )->get();

        $record = $this->resolveValue( $record, TRUE, FALSE );

        return response()->json( $record->first() );


    }

    public function form(Request $request)
    {

        $this->resource = $request->input( 'resource' );
        $search         = $request->input( 'search' );
        $primaryKey     = $request->input( 'primary_key' );

        $model = $this->resource()::getModelInstance();

        $record = $model->where( $primaryKey, $search )->get();

        $record = $this->resolveValue( $record, FALSE );

        return response()->json( $record->first() );


    }

}
