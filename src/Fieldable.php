<?php

namespace Prodemmi\Lava;


trait Fieldable {

    use CallableValue;

    public $showOnIndex = TRUE;

    public $showOnDetail = TRUE;

    public $showOnForms = TRUE;

    public $hide = FALSE;

    public function showOnAll ($showOnAll = TRUE)
    {

        if ( $showOnAll ) {

            $this->showOnIndex = TRUE;
            $this->showOnDetail = TRUE;
            $this->showOnForms = TRUE;

        }

        return $this;

    }

    public function hideOnAll ($hideOnAll = TRUE)
    {

        if ( $hideOnAll ) {

            $this->showOnIndex = FALSE;
            $this->showOnDetail = FALSE;
            $this->showOnForms = FALSE;

        }

        return $this;
    }

    public function onlyOnIndex ()
    {

        $this->showOnIndex = TRUE;
        $this->showOnDetail = FALSE;
        $this->showOnForms = FALSE;

        return $this;
    }

    public function onlyOnDetail ()
    {

        $this->showOnIndex = FALSE;
        $this->showOnDetail = TRUE;
        $this->showOnForms = FALSE;

        return $this;
    }

    public function onlyOnForms ()
    {

        $this->showOnIndex = FALSE;
        $this->showOnDetail = FALSE;
        $this->showOnForms = TRUE;

        return $this;
    }

    public function exceptOnDetail ()
    {

        $this->showOnIndex = TRUE;
        $this->showOnDetail = FALSE;
        $this->showOnForms = TRUE;

        return $this;
    }

    public function exceptOnForms ()
    {

        $this->showOnIndex = TRUE;
        $this->showOnDetail = TRUE;
        $this->showOnForms = FALSE;

        return $this;
    }

    public function exceptOnIndex ()
    {

        $this->showOnIndex = FALSE;
        $this->showOnDetail = TRUE;
        $this->showOnForms = TRUE;

        return $this;
    }

    public function hide ($hide = TRUE)
    {

        $this->hide = $this->callableValue($hide);

        return $this;
    }

}
