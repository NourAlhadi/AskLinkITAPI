<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);

        $token =  $user->createToken('askLinkIT')-> accessToken;
        return response()->json([
            'result' => 'success',
            'token' => $token
        ], $this-> successStatus);
    }

    /**
     * User Info API Endpoint
     *
     * @return \Illuminate\Http\Response
     */
    public function user(){

        $user = Auth::user();
        $avatar = url('') . '/images/' . $user->avatar;
        return response()->json([
            'result' => 'success',
            'user' => $user,
            'avatar' => $avatar
        ], $this-> successStatus);
    }

    /**
     * Edit User Info API Endpoint
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function userEdit(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'email|unique:users',
            'avatar' => 'file|max:4096',
            'name' => 'min:2',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'result' => 'error',
                'message'=>$validator->errors()
            ], $this->unAuthorised);
        }

        $user = Auth::user();
        if (!is_null($request->get('name'))) $user->name = $request->get('name');
        if (!is_null($request->get('email'))) $user->email = $request->get('email');
        if ($request->hasFile('avatar')) {

            $image = $request->file('avatar');
            $name = md5(uniqid($image->getBasename())).'.'.$image->guessExtension();
            $destinationPath = public_path('/images');
            $image->move($destinationPath, $name);
            $user->avatar = $name;
        }
        $user->save();

        $avatar = url('') . '/images/' . $user->avatar;
        return response()->json([
            'result' => 'success',
            'user' => $user,
            'avatar' => $avatar
        ],$this->successStatus);

    }

    /**
     * Get User Avatar API Endpoint
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function getAvatar(){
        $user = Auth::user();
        return response()->file(public_path('/images/' . $user->avatar));
    }


    /**
     * Set User Avatar API Endpoint
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setAvatar(Request $request){
        $user = Auth::user();
        if ($request->hasFile('avatar')) {

            $image = $request->file('avatar');
            $name = md5(uniqid($image->getBasename())).'.'.$image->guessExtension();
            $destinationPath = public_path('/images');
            $image->move($destinationPath, $name);
            $user->avatar = $name;

            $avatar = url('') . '/images/' . $user->avatar;
            return response()->json([
                'result' => 'success',
                'avatar' => $avatar
            ],$this->successStatus);
        }else{
            // Error Code 422 for Unprocessable Entity
            return response()->json([
                'result' => 'error',
                'message' => 'Missing the new avatar'
            ],422);
        }
    }


}