<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Get profile editable date
     *
     * @return [type]             [description]
     */
    public function getProfile(Request $request)
    {
        $profile = Auth::user();

        return $profile;
    }

    /**
     * Update profile
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        $this->validate($request, [
            'name'    => 'required|string|max:255',
            'phone'   => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'notes'   => 'nullable|string|max:1000',
        ]);

        $profile = Auth::user();

        $profile->update([
            'name'    => $request->name,
            'phone'   => $request->phone,
            'address' => $request->address,
            'notes'   => $request->notes,
        ]);

        return $profile->only(['name','phone','address']);
    }

    /**
     * Delete account
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteAccount(Request $request)
    {
        $user = Auth::user();

        if($user->hasRole(['administrator'])) {
            // 401 ce inicirati redirekciju (vidi api.js), zato sam stavio 404
            abort(404, 'An administrator account is not allowed to be deleted.');
            return;
        }

        // brisemo user account
        $user->delete();

        return 'account deleted';
    }
}
