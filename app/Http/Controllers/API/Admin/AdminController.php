<?php

namespace App\Http\Controllers\API\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminController extends Controller{


    public function __construct(){
        $this->middleware('role:administrator|superadministrator');
    }

    public function check(){
        return response()->json([
            'result' => 'success',
            'message' => 'admin bro'
        ]);
    }
}
