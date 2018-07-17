<?php

use Illuminate\Foundation\Inspiring;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

//Artisan::command('inspire', function () {
//    $this->comment(Inspiring::quote());
//})->describe('Display an inspiring quote');
Artisan::command('sync:hhx', function () {
    \App\Handlers\RedisHandler::Hhx();
})->describe('试一试');
Artisan::command('clear:today', function () {
    \App\Handlers\RedisHandler::ClearToday();
})->describe('定时清零today');
Artisan::command('sync:today', function () {
    \App\Handlers\RedisHandler::SyncToday();
})->describe('同步today');