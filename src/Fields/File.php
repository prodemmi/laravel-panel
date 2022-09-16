<?php

namespace Prodemmi\Lava\Fields;

class File extends Field
{

    public $disk = 'public';

    public $component = 'file';

    public $storeFileName;

    public $onUpload;

    public $onUpdate;

    public $onDelete;

    public $maxFiles = 1;

    public $acceptTypes = [];

    public $file = True;

    protected $fileType = 'document';

    public function __construct($name, $column = NULL)
    {

        parent::__construct($name, $column);
        
        $this->hideFromExport();

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

    public function onUpload($onUpload)
    {

        $this->onUpload = $onUpload;

        return $this;

    }

    public function onDelete($onDelete)
    {

        $this->onDelete = $onDelete;

        return $this;

    }

    public function onUpdate($onUpdate)
    {

        $this->onUpdate = $onUpdate;

        return $this;

    }

    public function multiple($maxFiles = PHP_INT_MAX)
    {

        $max = $this->callableValue( $maxFiles );

        if($max > 1){
            $this->maxFiles = $max ?: 1;
            return $this->exceptOnIndex()->noSqlSelect();

        }

        return $this;

    }


    public function acceptTypes(...$acceptTypes)
    {

        $this->acceptTypes = $acceptTypes;

        return $this->rules( [
            'mimetypes:' . implode( ',', $acceptTypes )
        ] );

    }

    public function image(){

        $this->fileType = 'image';

        $this->classes('rounded-full');

        return $this->acceptTypes('image/bmp', 'image/jpg', 'image/jpeg', 'image/png', 'image/webp');

    }

    public function video(){

        $this->fileType = 'video';
        $this->exceptOnIndex();

        return $this->acceptTypes('video/mp4');

    }

    public function audio(){

        $this->fileType = 'audio';
        $this->exceptOnIndex();

        return $this->acceptTypes('audio/mp3');

    }

    public function toArray()
    {

        return array_merge( parent::toArray(), [
            'maxFiles'    => $this->maxFiles,
            'multiple'    => $this->maxFiles > 1,
            'disk'        => $this->disk,
            'file'        => $this->file,
            'fileType'    => $this->fileType,
            'acceptTypes' => implode( ',', $this->acceptTypes ),
        ] );

    }

}