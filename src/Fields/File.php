<?php

namespace Prodemmi\Lava\Fields;

class File extends Relation
{

    public $disk = 'public';

    public $component = 'file';

    public $storeFileName;

    public $afterUpload;

    public $onDelete;

    public $onDownload;

    public $maxFiles = PHP_INT_MAX;

    public $acceptTypes = [];

    public $file = True;

    public function __construct($name, $resource, $relation = NULL)
    {
        
        parent::__construct( $name, $resource, $relation);

        if ( !$this->multiple ) {

            $this->maxFiles = 1;

        }

        $this->exceptOnIndex();

    }

    public function disk($disk)
    {

        $this->disk = $this->callableValue( $disk );

        return $this;

    }

    public function storeFileName($storeFileName)
    {

        $this->storeFileName = $storeFileName;

        return $this;

    }

    public function afterUpload($afterUpload)
    {

        $this->afterUpload = $afterUpload;

        return $this;

    }

    public function onDelete($onDelete)
    {

        $this->onDelete = $onDelete;

        return $this;

    }

    public function onDownload($onDownload)
    {

        $this->onDownload = $this->callableValue( $onDownload );

        return $this;

    }

    public function multiple($maxFiles = PHP_INT_MAX, $multiple = TRUE)
    {

        $this->multiple = $this->callableValue( $multiple );
        $this->maxFiles = $this->callableValue( $maxFiles );

        return $this;

    }

    public function acceptTypes(...$acceptTypes)
    {

        $this->acceptTypes = $acceptTypes;

        return $this->rules( [
            'mimetypes:' . implode( ',', $acceptTypes )
        ] );

    }


    public function toArray()
    {

        return array_merge( parent::toArray(), [
            'maxFiles'    => $this->maxFiles,
            'disk'        => $this->disk,
            'file'        => $this->file,
            'acceptTypes' => implode( ',', $this->acceptTypes ),
        ] );

    }

}