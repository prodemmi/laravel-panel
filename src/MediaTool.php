<?php

namespace Prodemmi\Lava;

use Prodemmi\Lava\Tools;

class MediaTool extends Tools
{

    public $icon       = 'video';
    public $group      = null;

    public function view()
    {

        return view('lava::tools.media-tool');
    }

    public function pluralLabel()
    {
        return "Media";
    }
}
