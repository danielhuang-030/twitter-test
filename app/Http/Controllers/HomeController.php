<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // /** @var \App\Models\User $userModel */
        // $userModel = app()->make(\App\Models\User::class);
        // /** @var \App\Models\User $user */
        // $user = $userModel->find(2);
        // $user->update(['name' => 'test002_updated1145']);
        // dd($user);

        return view('home');
    }
}
