<?php

use App\Router;

Router::get('/', 'HomeController@index');
Router::get('/test', 'HomeController@test');
Router::get('/load', 'HomeController@load');

Router::post('/', 'HomeController@test');
