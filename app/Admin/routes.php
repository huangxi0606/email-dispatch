<?php

use Illuminate\Routing\Router;



Route::group([
    'prefix'        => config('admin.route.prefix'),
//    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', function (){
        return redirect('/admin/email');
    });
    $router->post('/email/import', 'App\Admin\Controllers\EmailController@import');
    $router->get('/email/trash', 'App\Admin\Controllers\EmailController@trash');
    $router->get('/email/sync', 'App\Admin\Controllers\EmailController@sync');
    $router->get('/schedule/sync', 'App\Admin\Controllers\ScheduleController@sync');
    $router->resource('/email','App\Admin\Controllers\EmailController');
    $router->resource('/schedule','App\Admin\Controllers\ScheduleController');
});
Admin::registerAuthRoutes();