<?php

use App\Router;

Router::get('/', 'HomeController@index');
Router::get('/sources', 'HomeController@sources');

Router::generateRoutes('/api/sources', 'SourcesController', 'source_id');
