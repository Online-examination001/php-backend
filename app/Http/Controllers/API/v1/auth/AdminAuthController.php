<?php

namespace App\Http\Controllers\API\v1\auth;

use App\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AdminAuthController extends Controller
{





    public function register(Request $request)
    {
        $admin = new Admin;
        $validate = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:admins',
            'password' => 'required|min:6|confirmed',
        ]);
        $password = bcrypt($request->password);
        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->password = $password;
        $admin->save();
        $credentials = request(['email', 'password']);

        $data = new Admin();
        $data->id = $admin->id;
        $data->name = $admin->name;
        $data->email = $admin->email;
        $data->created_at = $admin->created_at;
        $data->updated_at = $admin->updated_at;

        $token = $this->guard()->setTTL(7200)->attempt($credentials);
        $bearer = 'bearer';
        $expires_in = auth('api')->factory()->getTTL() * 60;
        return response()->json(compact('data', 'token', 'bearer', 'expires_in'), 200);
 }




    public function login()
    {
        $credentials = request(['email', 'password']);
        if ($token = $this->guard()->setTTL(7200)->attempt($credentials)) {

            $bearer = 'bearer';
            $expires_in = auth('api')->factory()->getTTL() * 60;
            return response()->json(compact('token', 'bearer', 'expires_in'), 200);
        } else {
            return response()->json([
                'error' => 'Invalid Credentials'
            ]);
        }
    }


    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function guard()
    {
        return Auth::guard('admin');
    }

    public function me()
    {
        return response()->json(Auth::user());
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }
}
