<?php

namespace App\Http\Controllers\API\v1\auth;

use App\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Exceptions\JWTException;

class AdminAuthController extends Controller
{





    public function register(Request $request)
    {
        $admin = new User();
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
        try{

            $token = $this->guard()->attempt($credentials);



        }
        catch(JWTException $e){
            return response()->json( ['error'=>'Could not create token'],500);
        }


        $bearer = 'bearer';
        $expires_in = auth('api')->factory()->getTTL() * 60;
        return response()->json(compact('data', 'token', 'bearer', 'expires_in'), 200);
 }




    public function login()
    {
        $credentials = request(['email', 'password']);

        if ($token = auth('admin')->attempt($credentials)) {
            $bearer = 'bearer';
            $expires_in = auth('admin')->factory()->getTTL() * 60;
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


public function update_account(Request $request){
    $validate = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:admins',
            'password' => 'required|min:6|confirmed',
    ]);

    $admin = Auth::user();
    $admin->id = $admin->id;
    $admin->name = $request->name;
    $admin->email = $request->email;
    $admin->password =bcrypt($request->password);
    #Update credentials
    $admin->update();


    #Data data to be returned(excluding password)
    $data = new Admin();
    $data->id = $admin->id;
    $data->name = $admin->name;
    $data->email = $admin->email;
    $data->created_at = $admin->created_at;
    $data->updated_at = $admin->updated_at;
    $message = "You have been logged out, Please log in again with new credentials";
    $success = "Your account has been successfully updated";
    return response()->json(compact('data','success','message'));


}

public function destroy(){
    $admin_acct = Auth::user();
    $admin_acct->delete();
    auth()->logout();
    $success = "Your account has been successfully deleted";
    return response()->json(compact('success'));

}

}
