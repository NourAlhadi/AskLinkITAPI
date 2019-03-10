<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;

class UserController extends Controller{

    public $successStatus = 200;
    public $unAuthorised = 401;

    /**
     * Login API Endpoint
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request){

        $username = $request->get('username');
        $password = $request->get('password');

        if(Auth::attempt(['username' => $username, 'password' => $password])){
            $user = Auth::user();
            $token =  $user->createToken('askLinkIT')-> accessToken;
            return response()->json([
                'result' => 'success',
                'token' => $token,
            ], $this-> successStatus);
        }
        else{
            return response()->json([
                'result' => 'error',
                'message'=>'Unauthorised'
            ], $this->unAuthorised);
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
            'email' => 'required|email|unique:users',
            'username' => 'required|unique:users',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'result' => 'error',
                'message'=>$validator->errors()
            ], $this->unAuthorised);
        }
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $token =  $user->createToken('askLinkIT')-> accessToken;
        return response()->json([
            'result' => 'success',
            'token' => $token
        ], $this-> successStatus);
    }

    /**
     * details api
     *
     * @return \Illuminate\Http\Response
     */
    public function details(){

        $user = Auth::user();
        return response()->json([
            'result' => 'success',
            'user' => $user
        ], $this-> successStatus);
    }
}