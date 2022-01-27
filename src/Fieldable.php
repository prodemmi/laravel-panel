<?php

namespace Prodemmi\Lava;


trait Fieldable
{

    use CallableValue;

    public $showOnIndex = TRUE;

    public $showOnDetail = TRUE;

    public $showOnForm = TRUE;

    public $hideDefault = FALSE;

    public function showOnAll($showOnAll = TRUE)
    {

        if($showOnAll){

            $this->showOnIndex = true;
            $this->showOnDetail = true;
            $this->showOnForm = true;

        }

        return $this;
        
    }

    public function setOnIndex($setOnIndex = TRUE)
    {

        $this->showOnIndex = $this->callableValue( $setOnIndex );

        return $this;
    }

    public function setOnDetail($setOnDetail = TRUE)
    {

        $this->showOnDetail = $this->callableValue( $setOnDetail );


        return $this;
    }

    public function setOnForm($setOnForm = TRUE)
    {

        $this->showOnForm = $this->callableValue( $setOnForm );

        return $this;
    }

    public function onlyOnIndex()
    {

        $this->showOnIndex  = TRUE;
        $this->showOnDetail = FALSE;
        $this->showOnForm   = FALSE;

        return $this;
    }

    public function onlyOnDetail()
    {

        $this->showOnIndex  = FALSE;
        $this->showOnDetail = TRUE;
        $this->showOnForm   = FALSE;

        return $this;
    }

    public function onlyOnForms()
    {

        $this->showOnIndex  = FALSE;
        $this->showOnDetail = FALSE;
        $this->showOnForm   = TRUE;

        return $this;
    }

    public function exceptOnDetail()
    {

        $this->showOnIndex  = TRUE;
        $this->showOnDetail = TRUE;
        $this->showOnForm   = FALSE;

        return $this;
    }

    public function exceptOnForms()
    {

        $this->showOnIndex  = TRUE;
        $this->showOnDetail = TRUE;
        $this->showOnForm   = FALSE;

        return $this;
    }

    public function exceptOnIndex()
    {

        $this->showOnIndex  = FALSE;
        $this->showOnDetail = TRUE;
        $this->showOnForm   = TRUE;

        return $this;
    }

    public function hideDefault($hideDefault = TRUE)
    {

        $this->hideDefault = $this->callableValue( $hideDefault );

        return $this;
    }

}