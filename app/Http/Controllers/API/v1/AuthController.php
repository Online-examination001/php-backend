<?php

namespace App\Http\Controllers\API\v1;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request){
        $user = new User;
        $validator = Validator::make($request->all(),[
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
        $erros= $validator->errors();
        if ($validator->fails()){
            $status = 400;
            return response()->json(compact('erros','status'),400);
        }
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

        $token = $this->guard()->attempt($credentials);
        $bearer = 'bearer';
        $expires_in = auth('api')->factory()->getTTL() * 60;
        $status = http_response_code(201);
        return response()->json(compact('data', 'token', 'bearer', 'expires_in','status'), $status);

    }



    public function login()
    {
        $credentials = request(['email', 'password']);

        if ($token = $this->guard()->attempt($credentials)) {
            $bearer = 'bearer';
            $expires_in = auth('api')->factory()->getTTL() * 60;
            $status = http_response_code(200);
            return response()->json(compact('token', 'bearer', 'expires_in','status'), $status);
        }

       else{
            $status = http_response_code(401);
           return response()->json([
               'error'=>'Invalid Credentials'

           ],$status);
       }
    }


    public function logout()
    {
        auth()->logout();
        $status = 200;
        return response()->json(['message' => 'Successfully logged out','status' => $status],$status);
    }

    public function guard()
    {
        return Auth::guard('api');
    }

    public function me()
    {
        $status = 200;
        return response()->json(Auth::user(),$status);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    public function update(Request $request)
    {
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
        $user = Auth::user();
        $user->id = $user->id;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        #Update credentials
        $user->update();


        #Data data to be returned(excluding password)
        $data = new User();
        $data->id = $user->id;
        $data->name = $user->name;
        $data->email = $user->email;
        $data->created_at = $user->created_at;
        $data->updated_at = $user->updated_at;
        $message = "You have been logged out, Please log in again with new credentials";
        $success = "Your account haas been successfully updated";
        $status = 201;
        return response()->json(compact('data', 'success', 'message'),$status);
    }

    public function destroy()
    {
        $user_acct = Auth::user();
        $user_acct->delete();
        auth()->logout();
        $success = "Your account has been successfully deleted";
        $status = 200;
        return response()->json(compact('success','status'),$status);

    }
}
