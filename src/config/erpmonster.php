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
        'auth:api'
    ],

    /**
     *  URI prefix 
     *  ---------------------------------------
     *  api_uri_prefix='v1'  -> domain.com/v1/something 
     *  api_uri_prefix='v2'  -> domain.com/v2/something
     *  api_uri_prefix=''    -> domain.com/something
     * 
     */

    'api_uri_prefix' => '',

    'resource_namespace' => 'resources'
];
