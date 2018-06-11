<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function loadEditor(){
        $editor=env('APP_EDITOR','none');
        if(!is_dir(resource_path("views\\editor\\{$editor}"))) $editor='none';
        $editor_head="editor.{$editor}.head";
        $editor_container="editor.{$editor}.container";
        $editor_js="editor.{$editor}.js";
        return array($editor_head,$editor_container,$editor_js);
    }
}
