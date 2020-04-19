<?php

namespace App\Http\Controllers\API\v1;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{






    public function register(Request $request){
        $user = new User;
        $validate = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
        $password = bcrypt($request->password);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = $password;
        $user->save();
        $credentials = request(['email', 'password']);

        $data = new User();
        $data->id = $user->id;
        $data->name = $user->name;
        $data->email = $user->email;
        $data->created_at = $user->created_at;
        $data->updated_at = $user->updated_at;

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
        }

       else{
           return response()->json([
               'error'=>'Invalid Credentials'
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
        return Auth::guard('api');
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
