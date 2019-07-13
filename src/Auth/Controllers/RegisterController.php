<?php

namespace Erpmonster\Auth\Controllers;

use Api\Users\Models\User;

use Erpmonster\Http\Controller;
use Illuminate\Http\Request;


class RegisterController extends Controller
{
    use IssueTokenTrait;

    /**
     * @param Request $request
     * @return mixed
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => request('name'),
            'email'  => request('email'),
            'password' => bcrypt(request('password'))
        ]);

        return $this->issueToken($request, 'password', '*');

    }
}
