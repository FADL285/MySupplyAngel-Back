<?php

namespace App\Http\Controllers\Api\Dashboard\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Dashboard\Auth\LoginRequest;
use App\Http\Resources\Api\Dashboard\Admin\AdminResource;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $token = auth('api')->attempt($request->validated());

        if (! $token)
        {
            return response()->json(['status' => false, 'data' => null, 'message' => trans('dashboard.auth.credentials_not_found')], 403);
        }

        $user = auth('api')->user();

        data_set($user, 'token', $token);

        return (new AdminResource($user))->additional(['status' => true, 'message' => '']);
    }

    public function logout()
    {
        auth('api')->logout();
        return response()->json(['status' => true, 'data' => null, 'message' => trans('dashboard.auth.logout_success')]);
    }
}
