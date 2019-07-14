<?php

/**
 * Created by PhpStorm.
 * User: nikola.marcic
 * Date: 2/20/18
 * Time: 02:01
 */

return [
    'namespaces' => [
        'Api' => base_path() . DIRECTORY_SEPARATOR . 'app/api',
        'Erpmonster' => base_path() . DIRECTORY_SEPARATOR . 'vendor/erpmonster/laravel-toolbox/src'

    ],

    'protection_middleware' => [
        'jwt:api'
    ],

    'resource_namespace' => 'resources'
];
