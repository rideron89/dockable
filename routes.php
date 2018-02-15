<?php

use App\Routing\Router;

/*------------------------------------------------------------*
 |
 |    Front-end end-points
 |
 *------------------------------------------------------------*/
Router::get('/',         'HomeController@index');
Router::get('/login',    'HomeController@login');
Router::get('/logout',   'HomeController@logout');
Router::get('/register', 'HomeController@register');
Router::get('/clients',  'HomeController@clients');

/*------------------------------------------------------------*
 |
 |    Sources API end-points
 |
 *------------------------------------------------------------*/
Router::get(   '/api/sources',             'SourcesController@index' )->middleware('BasicAuth');
Router::get(   '/api/sources/{source_id}', 'SourcesController@read'  )->middleware('BasicAuth');
Router::post(  '/api/sources',             'SourcesController@create')->middleware('BasicAuth');
Router::put(   '/api/sources/{source_id}', 'SourcesController@update')->middleware('BasicAuth');
Router::delete('/api/sources/{source_id}', 'SourcesController@delete')->middleware('BasicAuth');

/*------------------------------------------------------------*
 |
 |    Auth API end-points
 |
 *------------------------------------------------------------*/
Router::post('/api/login',  'AuthController@login');
Router::post('/api/register', 'AuthController@register');

/*------------------------------------------------------------*
 |
 |    Client OAuth end-points
 |
 *------------------------------------------------------------*/
 Router::post('/oauth/register', 'ClientsController@register')->middleware('BasicAuth');
 Router::post('/oauth/unregister', 'ClientsController@unregister')->middleware('BasicAuth');
 Router::put('oauth/update-client', 'ClientsController@update')->middleware('BasicAuth');
 Router::post('/oauth/reset-client', 'ClientsController@reset')->middleware('BasicAuth');
