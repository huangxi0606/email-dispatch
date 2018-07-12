<?php

namespace App\Admin\Extensions;

use Encore\Admin\Form\Field;

class HighChart extends Field
{
    protected $view = 'admin.highchart';

    protected static $css = [
//        '/vendor/Highcharts-6.0.1/code/highcharts.js',
    ];

    protected static $js = [
        '/vendor/Highcharts-6.0.1/code/highcharts.js',
//        '/vendor/Highcharts-6.0.1/code/modules/exporting.js',
        '/vendor/Highcharts-6.0.1/code/draggable-points.js',
    ];

    public function render()
    {
        return parent::render();
    }
}