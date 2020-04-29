<?php

namespace App\Http\Controllers\API\v1\auth;

use App\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;

class AdminAuthController extends Controller
{





    public function register(Request $request)
    {
        $admin = new User();
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
        $erros = $validator->errors();
        if ($validator->fails()) {
            $status = 400;
            return response()->json(compact('erros','status'),$status);
        }
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
            return response()->json( ['error'=>'Could not create token','status'=>500],500);
        }

        $status = 201;
        $bearer = 'bearer';
        $expires_in = auth('api')->factory()->getTTL() * 60;
        return response()->json(compact('data', 'token', 'bearer', 'expires_in','status'), 201);
 }




    public function login()
    {
        $credentials = request(['email', 'password']);

        if ($token = auth('admin')->attempt($credentials)) {
            $bearer = 'bearer';
            $expires_in = auth('admin')->factory()->getTTL() * 60;
            $status = 200;
            return response()->json(compact('token', 'bearer', 'expires_in','status'), $status);
             }
              else {

            $status = 401;

            return response()->json([
                'error' => 'Invalid Credentials',
                'status' => $status
            ],$status);
        }
    }


    public function logout()
    {
        auth()->logout();
        $status = 200;
        return response()->json(['message' => 'Successfully logged out','status'=>$status],200);
    }

    public function guard()
    {
        return Auth::guard('admin');
    }

    public function me()
    {
        $status = http_response_code(200);
        return response()->json(Auth::user(),$status);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }


public function update_account(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
        $erros = $validator->errors();
        if ($validator->fails()) {
            $status = 400;
            return response()->json(compact('erros','status'),$status);
        }
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
        $status = 200;
    return response()->json(compact('data','success','message','status'),$status);


}

public function destroy(){
    $admin_acct = Auth::user();
    $admin_acct->delete();
    auth()->logout();
    $success = "Your account has been successfully deleted";
        $status = 200;
    return response()->json(compact('success'),$status);

}

}
