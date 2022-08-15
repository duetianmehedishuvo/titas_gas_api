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
    return '<h1>Welcome Titas Gas Service Api Limited. Creator By Mehedi Hasan Shuvo</h1>';
});


$router->group(['prefix'=>'api'],function() use ($router){

   // TODO: for Authentication
    $router->post('/register', 'RegistrationController@onRegister');
    $router->post('/login', 'LoginController@onLogin');
    $router->post('/updateUser', 'RegistrationController@updateUser');


   $router->group(['middleware'=>'auth'],function() use ($router){
    
    // TODO: for Test Token
        $router->post('/tokenTest', 'LoginController@tokenTest');
        $router->get('/allUser', 'RegistrationController@getAllUser');
        $router->get('/getUserByID', 'RegistrationController@getUserByID');

    // TODO: for Transaction
        $router->get('/getAllTranaction','TransactionController@getAllTransctionList');
        $router->post('/addTranaction','TransactionController@addTranaction');
        $router->get('/getTranactionById','TransactionController@getTranactionByID');
        

    // TODO: for Meter
        $router->get('/meter', 'MeterController@onSelect');
        $router->post('/meter', 'MeterController@onInsert');
        $router->get('/deletemeterByID', 'MeterController@deleteMeter');
        $router->get('/meterByID', 'MeterController@getMeterByID');
        $router->post('/meterUpdate', 'MeterController@updateMeter');
   });
});
