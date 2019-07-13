<?php
/**
 * Created by PhpStorm.
 * User: nikola.marcic
 * Date: 5/1/18
 * Time: 18:46
 *
 */
namespace Erpmonster\Auth\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

trait IssueTokenTrait {

    public function issueToken(Request $request, $grantType, $scope="*")
    {
        $params =[
            'grant_type' => $grantType,
            'client_id' => env('CLIENT_ID'),
            'client_secret'=> env('CLIENT_SECRET'),
            'scope' => $scope
        ];

        if($grantType !== 'social'){
            $params['username'] = $request->username ?: $request->email;
        }
        $request->request->add($params);

        $proxy =  Request::create('oauth/token', 'POST');

        return Route::dispatch($proxy);
    }

}
