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
        return [
            'user' => Auth::user(),
            'config' => [
                'db_host' => config('tenants.db_host'),
                'redis_host' => config('tenants.redis_host'),
                'timezone' => config('tenants.timezone'),
                'domain' => config('digitalocean.app_domain'),
                'versions' => $this->getVersions(),
                'timezones' => $this->getTimezones(),
            ]
        ];
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

    /**
     * Get app versions
     *
     * @return \Illuminate\Http\Response
     */
    public function getVersions()
    {
        $appsPath = config('tenants.path') . '/apps';
        $appsList = scandir($appsPath);

        return array_values(array_filter($appsList, fn($item) => is_dir($appsPath . '/' . $item) && $item !== '.' && $item !== '..'));
    }

    /**
     * Get Linux timezones
     *
     * @return \Illuminate\Http\Response
     */
    public function getTimezones()
    {
        $output = null;
        $retval = null;
        exec('timedatectl list-timezones', $output, $retval);

        return $output;
    }

}
