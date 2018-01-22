<?php

use App\Router;

Router::get('/', 'HomeController@index');
Router::get('/test', 'HomeController@test');

Router::post('/', 'HomeController@test');
