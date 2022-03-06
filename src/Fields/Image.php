<?php

namespace Prodemmi\Lava\Fields;

use App\Models\Media;

class Image extends File
{

    public function __construct($name, $resource, $relation = NULL)
    {

        parent::__construct($name, $resource, $relation = NULL);
        $this->acceptTypes( 'image/*' )->hideFromExport()->showOnAll();
        
    }

    public function rounded()
    {

        return $this->classes('rounded-full');

    }

}