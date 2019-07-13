<?php

$router->post('/auth/logout', 'JwtLoginController@logout');
$router->post('/auth/refresh', 'JwtLoginController@refresh');
$router->get('/me', 'JwtLoginController@me');
