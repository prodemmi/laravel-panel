<?php

namespace Prodemmi\Lava;

use Prodemmi\Lava\Tools;

class MediaTool extends Tools
{

    public $icon       = 'video';
    public $route       = 'file-manager';
    public $group      = null;
    public $scripts      = ['https://unpkg.com/vue-simple-context-menu/dist/vue-simple-context-menu.min.js'];
    public $styles       =  ['https://unpkg.com/vue-simple-context-menu/dist/vue-simple-context-menu.css'];

    public function label()
    {

        return 'File manager';
    }

    public function view()
    {

        return view('lava::tools.media-tool');
    }

    public function pluralLabel()
    {
        return "Media";
    }
}
