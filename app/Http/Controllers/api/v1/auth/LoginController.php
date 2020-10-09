<?php

namespace App\Http\Controllers\api\v1\auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\api\v1\LoginRequest;
use App\Http\Resources\users\UserResource;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Login user.
     *
     * @param LoginRequest $request
     * @return void
     */
    public function __invoke(LoginRequest $request)
    {
        if (! Auth::attempt($request->validated())) {
            return response(['message' => 'Invalid credentials.']);
        }

        //$user = $request->user();

        $user = Auth::user();

        $user->tokens->each(function ($token, $key) {
            $token->delete();
        });

        $token = $user->createToken('authToken')->accessToken;

        return response([
            'user' => UserResource::make(Auth::user()),
            'access_token' => $token
        ]);
    }
}
