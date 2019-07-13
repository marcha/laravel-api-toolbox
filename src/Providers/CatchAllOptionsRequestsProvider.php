<?php
/**
 * Created by PhpStorm.
 * User: nikola.marcic
 * Date: 2/22/18
 * Time: 02:04
 */

namespace Erpmonster\Providers;


use Illuminate\Support\ServiceProvider;
/**
 * If the incoming request is an OPTIONS request
 * we will register a handler for the requested route
 */
class CatchAllOptionsRequestsProvider extends ServiceProvider {

    public function register()
    {
        $request = app('request');

        if ($request->isMethod('OPTIONS'))
        {
            app()->options($request->path(), function() { return response('', 200); });
        }
    }

}
