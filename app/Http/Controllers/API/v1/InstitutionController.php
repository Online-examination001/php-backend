<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\InstitutionResource;
use App\Institution;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class InstitutionController extends Controller
{
    public function index(){
        $institutions = Institution::all();
        return InstitutionResource::collection($institutions);
    }

    public function show()
    {   $user = Auth::user();
        $institution = Institution::findOrFail('user_id'== $user->id);
            return new InstitutionResource($institution);
    }
    public function adminShow($id)
    {
        $institution = Institution::findOrFail('id' == $id);
        return new InstitutionResource($institution);
    }

    public function create(Request $request){
        $user = Auth::user();
        $institution_qs = Institution::findOrFail('user_id'== $user->id);
        if($institution_qs== null){
            return response()->json(['error'=>'This account cannot register two unstitutions','status'=>409],409);
        }
        $institution = new Institution;
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:250',
            'abbreviated_name' => 'required|max:500',
        ]);
        $erros = $validator->errors();
        if ($validator->fails()) {
            $status = 400;
            return response()->json(compact('erros','status'),400);
        }
        $user = Auth::user();
        $institution->user_id = $user->id;
        $institution->name = $request->name;
        $institution->subscribed_api_left = 0;
        $institution->abbreviated_name = $request->abbreviated_name;
        $institution->save();
        $message = 'You have succesfully registerd an institution';
        $status = 201;
        return response()->json(compact('institution', 'message','status'), 201);

    }

    public function update(Request $request,$id)
    {
        $user = Auth::user();

        #Check if the institution instance to be updated is for the current user
        $institution_qs = Institution::find('user_id' == $user->id);
        if (!$institution_qs == null) {
            $status = 404;
            return response()->json(['Message'=>'You are not authorized to update this account','status'],404);
        }

        #Check if the id passed in the url exists in the data base
        $institution = Institution::findOrFail('id' == $id);
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:250',
            'abbreviated_name' => 'required|max:500',
        ]);
        $erros = $validator->errors();
        if ($validator->fails()) {
            $status = 400;
            return response()->json(compact('erros','status'),400);
        }
        $user = Auth::user();
        $institution->user_id = $user->id;
        $institution->name = $request->name;
        $institution->abbreviated_name = $request->abbreviated_name;
        $institution->subscribed_api_left = $institution->subscribed_api_left;
        $institution->save();
        $message = 'Succesfully Updated the institution account';
        $status = 200;
        return response()->json(compact('institution', 'message','status'), 200);
    }
}


