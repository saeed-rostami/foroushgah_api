<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use App\Http\Controllers\Admin\Content\CategoryController;

$router->get('/', function () use ($router) {
    return $router->app->version();
});

//ADMIN
$router->group(['namespace' => 'Admin', 'prefix' => 'admin'], function () use ($router) {
    //CONTENT
    $router->group(['namespace' => 'Content', 'prefix' => 'content'], function () use ($router) {
//        category
        $router->group(['prefix' => 'category'], function () use ($router) {
            $router->get('/', 'CategoryController@index');
            $router->delete('/{id}', 'CategoryController@destroy');
            $router->post('/', 'CategoryController@store');
            $router->put('/{id}', 'CategoryController@update');
        });
//        post
        $router->group(['prefix' => 'post'], function () use ($router) {
            $router->get('/', 'PostController@index');
            $router->delete('/{id}', 'PostController@destroy');
            $router->post('/', 'PostController@store');
            $router->put('/{id}', 'PostController@update');
        });
//        menu
        $router->group(['prefix' => 'menu'], function () use ($router) {
            $router->get('/', 'MenuController@index');
            $router->delete('/{id}', 'MenuController@destroy');
            $router->post('/', 'MenuController@store');
            $router->put('/{id}', 'MenuController@update');
        });
        //        faq
        $router->group(['prefix' => 'faq'], function () use ($router) {
            $router->get('/', 'FAQController@index');
            $router->delete('/{id}', 'FAQController@destroy');
            $router->post('/', 'FAQController@store');
            $router->put('/{id}', 'FAQController@update');
        });
    });
});
