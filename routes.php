<?php

use App\Routing\Router;

/*------------------------------------------------------------*
 |
 |    Front-end end-points
 |
 *------------------------------------------------------------*/
Router::get('/',         'HomeController@index');
Router::get('/login',    'HomeController@login');
Router::get('/register', 'HomeController@register');

/*------------------------------------------------------------*
 |
 |    Auth API end-points
 |
 *------------------------------------------------------------*/
Router::post('/api/login',  'AuthController@login');
Router::post('/api/logout', 'AuthController@logout');
Router::post('/api/register', 'AuthController@register');

/*------------------------------------------------------------*
 |
 |    Token API end-points
 |
 *------------------------------------------------------------*/
Router::delete('/api/token/{token_id}', 'TokenController@delete')->middleware('TokenAuth');
