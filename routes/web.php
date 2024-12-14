<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/routes', function () {
    $routeCollection = Route::getRoutes();
    $routes = [];
    foreach ($routeCollection as $route) {
        $routes[] = [
            'method' => $route->methods()[0],
            'uri' => $route->uri(),
            'name' => $route->getName(),
            'action' => $route->getActionName(),
        ];
    }
    return response()->json($routes);
});
