<?php

namespace Prodemmi\Lava\Fields;

use Facade\FlareClient\Http\Exceptions\MissingParameter;
use Illuminate\Support\Str;

class Relation extends Field
{

    public $component = 'relation';

    public $relationType;

    public $update_column;

    public $resource;

    protected $multiple = false;

    public static function create($name, $resource = NULL, $relation = NULL)
    {

        if ( blank( $resource ) ) {

            throw MissingParameter::create( 'resource' );

        }

        return new static( $name, $resource, $relation );
    }

    public function __construct($name, $resource, $relation = null)
    {

        if ( is_null( $relation ) ) {

            $relation = (string)Str::of( $name )->lower()->snake();

        }

        $this->resource     = $resource;

        parent::__construct( $name, $relation );

        $this->findRelationType()->exceptOnIndex()->noSqlSelect(); 

    }

    protected function findRelationType(){

        $caller = resolve((string)Str::of(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)[2]['file'])->replace(app_path(), '/')->ltrim('/')->rtrim('.php')->start('App\\')->replace('/', '\\'));

        $model = $caller->getModelInstance();
        
        $relationType = last((explode('\\', get_class($model->{$this->column}()))));

        $function = $model->{$this->column}();

        $this->multiple = !(Str::of($relationType)->lower()->contains('one') || !Str::of($relationType)->lower()->contains('many'));

        if($relationType === 'HasOne'){

            $update_column = $function?->getLocalKeyName();
            
        }elseif($relationType === 'BelongsTo' || $relationType === 'HasMany'){

            $update_column = $function?->getForeignKeyName();

        }else{

            $update_column = $function?->getForeignPivotKeyName();

        }

        if(!isset($update_column)){
            dd($relationType);
        }
        
        $this->update_column     = $update_column;
        $this->relationType      = $relationType;

        return $this;

    }

    public function toArray()
    {
        return array_merge( parent::toArray(), [
            'resource'        => $this->resource,
            'multiple'        => $this->multiple,
            'relationType'    => $this->relationType,
            'relation'        => true,
            'column'          => $this->column,
            'update_column'   => $this->update_column
        ] );
    }

}