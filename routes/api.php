<?php

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Router;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|


Auto-generated route..

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/

Route::prefix('/parttime')
    ->namespace('\App\Http\Controllers\Parttime')
    ->group(function(Router $router) {
        $CONTROLLER = "ParttimeController";
        $router->any('/view', "$CONTROLLER@view")->name("parttime.view");
        $router->post('/create', "$CONTROLLER@create")->name("parttime.create");
        $router->post('/delete', "$CONTROLLER@delete")->name("parttime.delete");
        $router->post('/cancel', "$CONTROLLER@cancel")->name("parttime.cancel");
        $router->post('/sign', "$CONTROLLER@sign")->name("parttime.sign");
        $router->post('/check', "$CONTROLLER@check")->name("parttime.check");
        $router->post('/unsign', "$CONTROLLER@unsign")->name("parttime.unsign");
    });

Route::prefix('/user')
    ->namespace('\App\Http\Controllers\Parttime')
    ->group(function (Router $router) {
        $CONTROLLER = "ParttimeUserController";
        $router->get('/view', "$CONTROLLER@view")->name('user.view');
        $router->post('/self', "$CONTROLLER@viewself")->name('user.viewself');
        $router->post('/created', "$CONTROLLER@viewCreateds")->name('user.viewcreateds');
        $router->post('/config', "$CONTROLLER@config")->name('user.config');
        $router->post('/register', "$CONTROLLER@register")->name('user.register');
        $router->post('/login', "$CONTROLLER@login")->name('user.login');
    });

Route::prefix('/record')
    ->namespace('\App\Http\Controllers\Parttime')
    ->group(function(Router $router) {
        $CONTROLLER = "ParttimeRecordController";
        $router->post('/view', "$CONTROLLER@view")->name('record.view');
    });

Route::prefix('/message')
    ->namespace('\App\Http\Controllers\Parttime')
    ->group(function(Router $router){
        $CONTROLLER = "ParttimeMessageController";
        $router->post('/view', "$CONTROLLER@view")->name('message.view');
        $router->post('/read', "$CONTROLLER@read")->name('message.read');
    });

Route::prefix("/code")
    ->namespace('\App\Http\Controllers\Parttime')
    ->group(function(Router $router){
        $CONTROLLER = "PhoneCodeController";
        $router->get('/allocate', "$CONTROLLER@allocate")->name('code.allocate');
    });