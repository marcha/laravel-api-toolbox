<?php

namespace Erpmonster\Auth\Controllers;

use Api\Users\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    use IssueTokenTrait;


    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required'
        ]);

        return $this->issueToken($request, 'password');

    }

    public function refresh(Request $request)
    {
        $this->validate($request, [
            'refresh_token' => 'required'
        ]);

        return $this->issueToken($request, 'refresh_token');

    }

    public function logout(Request $request)
    {
        $accessToken = $request->user()->token();

        DB::table('oauth_refresh_tokens')
            ->where('access_token_id', $accessToken->id)
            ->update([
                'revoked' => true
            ]);

        $accessToken->revoke();

        return response()->json([], 204);
    }
    
    public function me(Request $request)
    {   
        $user = $request->user();
        $data['name'] = $user->name;
        $data['email'] = $user->email;
        $data['id'] = $user->id;

        return response()->json($data, 200);
    }


}
