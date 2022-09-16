<?php

namespace Prodemmi\Lava;

use Prodemmi\Lava\Tool;

class FileManagerTool extends Tool
{

    public $icon         = 'video';
    public $route        = 'file-manager';
    public $group        = null;

    public function scripts(){
        return [
            asset('lava/tools/file-manager-tool/js/file-manager-tool.js')
        ];
    }

    public function styles(){
        return [
            asset('lava/tools/file-manager-tool/css/file-manager-tool.css')
        ];
    }

    public function label()
    {
        return 'File manager';
    }

    public function pluralLabel()
    {
        return "File manager";
    }
    
    public function view()
    {
        return view('lava::tools.file-manager-tool');
    }

    public function showWhen(): bool
    {
        return true;
    }
    
}
