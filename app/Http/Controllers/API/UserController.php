<?php

namespace App\Http\Controllers\API;

use App\Helpers\MediaHelper;
use App\Role;
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
            $message = "";
            foreach ($validator->errors()->keys() as $key){
                foreach($validator->errors()->get($key) as $msg) {
                    $message .= $msg . htmlspecialchars('\n');
                }
            }
            $message = str_replace("\\n","\n",$message);
            return response()->json([
                'result' => 'error',
                'message'=>$message
            ], $this->unAuthorised);
        }

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);
        $user->attachRole(Role::find(3));

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
    public function user(MediaHelper $helper){

        $user = Auth::user();
        $avatar = $helper->getUploadedFileUrl($user->avatar,'images');
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
    public function userEdit(Request $request, MediaHelper $helper){
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
            $name = $helper->uploadFile($image,'images');
            $user->avatar = $name;
        }
        $user->save();

        $avatar = $helper->getUploadedFileUrl($user->avatar,'images');
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
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getAvatar(MediaHelper $helper){
        $user = Auth::user();
        return $helper->getUploadedFile($user->avatar,'images');
    }


    /**
     * Set User Avatar API Endpoint
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setAvatar(Request $request, MediaHelper $helper){
        $user = Auth::user();
        if ($request->hasFile('avatar')) {

            $image = $request->file('avatar');
            $name = $helper->uploadFile($image, 'images');
            $user->avatar = $name;
            $user->save();

            $avatar = $helper->getUploadedFileUrl($name,'images');
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