<?php

return [
    /**
     *  ---------------------------------------------------------
     *  Namespaces
     *  ---------------------------------------------------------
     *  Containers namespaces
     *  Api/
     *      Partners
     *      Clients
     *      SomeItems
     * 
     */

    'namespaces' => [
        'Api' => base_path() . DIRECTORY_SEPARATOR . 'app/api'
    ],

    /**
     *  ---------------------------------------------------------
     *  Protection middleware
     *  ---------------------------------------------------------
     *  Protection for routes in files routes.php or routes_protected.php
     *  Routes in file routes_public.php are unprotected
     * 
     */
    'protection_middleware' => [
        'auth:api-client'
    ],

    /**
     *  ---------------------------------------------------------
     *  URI prefix 
     *  ---------------------------------------------------------
     *  api_uri_prefix='v1'  -> domain.com/v1/something 
     *  api_uri_prefix='v2'  -> domain.com/v2/something
     *  api_uri_prefix=''    -> domain.com/something
     * 
     */

    'api_uri_prefix' => '',
];
