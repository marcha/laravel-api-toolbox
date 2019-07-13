<?php

$router->post('/auth/login', 'JwtLoginController@login');
$router->post('/auth/register', 'JwtLoginController@register');
$router->post('/auth/password/email', 'ForgotPasswordController@sendResetLinkEmail');
$router->post('/auth/password/reset', 'ResetPasswordController@reset');
