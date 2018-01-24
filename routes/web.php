<?php

use App\Routing\Router;

/*------------------------------------------------------------*
 |
 |    Front-end end-points
 |
 *------------------------------------------------------------*/
Router::get('/', 'HomeController@index');

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
Router::get('/api/auth', 'AuthController@index')->middleware(['BasicAuth']);
