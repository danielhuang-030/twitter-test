<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * info
     */
    public function info(Request $request)
    {
        return response()->json(auth()->user());
    }

    /**
     * follow
     */
    public function follow(Request $request)
    {
        return response()->json(auth()->user()->follows()->get());
    }

    /**
     * follow
     */
    public function followMe(Request $request)
    {
        return response()->json(auth()->user()->followMes()->get());
    }
}
