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

$router->get('/', function () use ($router) {
    return $router->app->version();
});


$router->group(['prefix'=>'api'],function() use ($router){

   // TODO: for Authentication
    $router->post('/register', 'RegistrationController@onRegister');
    $router->post('/login', 'LoginController@onLogin');

   $router->group(['middleware'=>'auth'],function() use ($router){
    
    // TODO: for Test Token
    $router->post('/tokenTest', 'LoginController@tokenTest');

    // TODO: for Transaction
        $router->get('/getAllTranaction','TransactionController@getAllTransctionList');
        $router->post('/addTranaction','TransactionController@addTranaction');
        $router->get('/getTranactionById','TransactionController@getTranactionByID');
        $router->get('/getTranactionqwq','TransactionController@getAllTranactionOrder');

    // TODO: for Meter
        $router->get('/all_meter', 'MeterController@onSelect');
        $router->post('/add_meter', 'MeterController@onInsert');
   });
});
