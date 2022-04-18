<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Index page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('index');
    }

    /**
     * Check if the user is authenticated
     *
     * @return null
     */
    public function check()
    {
        return Auth::user();
    }

    /**
     * Update user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function updateUserPassword(Request $request, \App\Models\User $user)
    {
        $this->validate($request, [
            'password' => 'required|confirmed',
        ]);

        $user->password = bcrypt($request->password);
        $user->save();

        return compact('user');
    }

}
