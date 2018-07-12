<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Grid\Tools\AbstractTool;

class SyncButton extends AbstractTool
{
    protected function script()
    {
    }

    public function render()
    {
        return view('admin.syncButton');
    }
}