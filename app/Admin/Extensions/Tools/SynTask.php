<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Tools\AbstractTool;
use Illuminate\Support\Facades\Request;

class SynTask extends AbstractTool
{
    protected function script()
    {
    }
    public function render()
    {
        return view('admin.synTask');
    }
}