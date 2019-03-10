<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;

class UserController extends Controller{

    public $successStatus = 200;

    /**
     * Login API Endpoint
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request){

        $email = $request->get('email');
        $password = $request->get('password');

        if(Auth::attempt(['email' => $email, 'password' => $password])){
            $user = Auth::user();
            $success['token'] =  $user->createToken('askLinkIT')-> accessToken;
            return response()->json(['success' => $success], $this-> successStatus);
        }
        else{
            return response()->json(['error'=>'Unauthorised'], 401);
        }
    }


    /**
     * Register API Endpoint
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('askLinkIT')-> accessToken;
        $success['name'] =  $user->name;
        return response()->json(['success'=>$success], $this-> successStatus);
    }

    /**
     * details api
     *
     * @return \Illuminate\Http\Response
     */
    public function details(){

        $user = Auth::user();
        return response()->json(['success' => $user], $this-> successStatus);
    }
}