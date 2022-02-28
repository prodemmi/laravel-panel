<?php

namespace Prodemmi\Lava\Fields;

use Facade\FlareClient\Http\Exceptions\MissingParameter;
use Illuminate\Support\Str;

class Relation extends Field
{

    public $component = 'relation';

    public $relation;

    public $resource;

    protected $multiple = false;

    public static function create($name, $resource = NULL, $relation = NULL)
    {

        if ( blank( $resource ) ) {

            throw MissingParameter::create( 'resource' );

        }

        $static = new static( $name, $resource, $relation );

        $static->resource = $resource;

        return $static;
    }

    public function __construct($name, $resource, $relation = null)
    {

        if ( is_null( $relation ) ) {

            $relation = (string)Str::of( $name )->lower()->snake();

        }

        $this->relation = $relation;
        $this->resource = $resource;

        $this->findRelationType(); 

        parent::__construct( $name, $relation );

        $this->exceptOnIndex()->noSqlSelect();

    }

    protected function findRelationType(){

        $caller = resolve((string)Str::of(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)[2]['file'])->replace(app_path(), '/')->ltrim('/')->rtrim('.php')->start('App\\')->replace('/', '\\'));

        $model = $caller->getModelInstance();
        
        $name = last((explode('\\', get_class($model->{$this->relation}()))));

        $function = $model->{$this->relation}();

        if(method_exists($function, 'getLocalKeyName')){

            $column = $function->getLocalKeyName();

        }elseif(method_exists($function, 'getOwnerKeyName')){

            $column = $function->getOwnerKeyName();

        }
        elseif(method_exists($function, 'getForeignPivotKeyName')){

            $column = $function->getForeignPivotKeyName();

        }
        else{

            dd($name);

        }
        
        $this->column     = $column;
        $this->relation   = $name;
        $this->multiple   = !(Str::of($name)->lower()->contains('one') || !Str::of($name)->lower()->contains('many'));

    }

    public function toArray()
    {
        return array_merge( parent::toArray(), [
            'resource' => $this->resource,
            'multiple' => $this->multiple,
            'relation' => $this->relation
        ] );
    }

}