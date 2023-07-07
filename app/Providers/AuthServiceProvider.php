<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

use App\Traits\HasAuth;

class AuthServiceProvider extends ServiceProvider
{
    use HasAuth;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        $this->app['auth']->viaRequest('api', function ($request) {
            $token = $this->token($request);
            if ($token) {
                $user = DB::table('users')->where('token', $token)->first();
                // $apps = DB::table('personal_access_clients')->where('token', $token)->first();
                return $user;
            }
        });
    }
}
