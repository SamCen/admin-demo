<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\LoginRequest;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Author sam
     * DateTime 2019-05-30 16:23
     * Description:登录获取token
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $credentials = [
            'account'=>$request->get('account'),
            'password'=>$request->get('password'),
            'status' => Admin::STATUS_ENABLE,
        ];
        if (! $token = auth('admin')->attempt($credentials)) {
            return error('账号或密码不正确',401);
        }
        /**
         * @var $admin Admin
         */
        $admin = Auth::user();
        $admin->last_login_ip = isset($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:'127.0.0.1';
        $admin->save();
        $response = [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('admin')->factory()->getTTL() * 60
        ];
        return success($response);
    }

    /**
     * Author sam
     * DateTime 2019-05-23 15:06
     * Description:登出注销token
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('admin')->logout();
        return success();
    }
}
