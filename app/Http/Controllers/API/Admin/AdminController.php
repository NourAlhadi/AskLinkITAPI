<?php

namespace App\Http\Controllers\API\Admin;

use App\Role;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminController extends Controller{


    private $role_user;
    private $role_admin;
    private $role_super;

    public function __construct(){
        $this->middleware('role:superadministrator');
        $this->role_user = Role::find(3);
        $this->role_admin = Role::find(2);
        $this->role_super = Role::find(1);
    }

    public function makeAdmin(Request $request){

        $user = $request->get('user_id');
        $user = User::find($user);
        if (is_null($user)){
            return response()->json([
                'result' => 'error',
                'message' => 'User not found'
            ]);
        }
        if ($user->hasRole($this->role_admin->name)){
            $additional = '';
            if ($user->hasRole($this->role_super->name)){
                $user->detachRole($this->role_super);
                $additional = ' but not a super admin anymore';
            }
            return response()->json([
                'result' => 'success',
                'message' => 'User is already an admin' . $additional
            ]);
        }

        if ($user->hasRole($this->role_super->name)){
            $user->detachRole($this->role_super);
        }
        $user->attachRole($this->role_admin);

        return response()->json([
            'result' => 'success',
            'message' => 'User is now an admin'
        ]);
    }

    public function makeSuper(Request $request){

        $user = $request->get('user_id');
        $user = User::find($user);
        if (is_null($user)) {
            return response()->json([
                'result' => 'error',
                'message' => 'User not found'
            ]);
        }
        if ($user->hasRole($this->role_super->name)) {
            return response()->json([
                'result' => 'success',
                'message' => 'User is already a super admin'
            ]);
        }

        $user->attachRole($this->role_super);
        return response()->json([
            'result' => 'success',
            'message' => 'User is now a super admin'
        ]);
    }

    public function makeUser(Request $request){

        $user = $request->get('user_id');
        $user = User::find($user);
        if (is_null($user)) {
            return response()->json([
                'result' => 'error',
                'message' => 'User not found'
            ]);
        }
        if ($user->hasRole($this->role_super->name)) {
            $user->detachRole($this->role_super);
        }

        if ($user->hasRole($this->role_admin->name)) {
            $user->detachRole($this->role_admin);
        }

        if (! $user->hasRole($this->role_user->name)){
            $user->attachRole($this->role_user);
        }

        return response()->json([
            'result' => 'success',
            'message' => 'User is now a normal user'
        ]);
    }

    public function check(){
        return response()->json([
            'result' => 'success',
            'message' => 'admin bro'
        ]);
    }
}
