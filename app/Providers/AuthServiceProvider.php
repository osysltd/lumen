<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{

    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

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

        $this->app['auth']->viaRequest('web', function ($request) {
            return User::find(1);
        });


        $this->app['auth']->viaRequest('api', function ($request) {
            return User::find(1);
        });


        // $this->app['auth']->viaRequest('api', function ($request) {
        //     return app('auth')->setRequest($request)->user();
        // });

        // $this->app['auth']->viaRequest('token', function ($request) {
        //     if ($request->bearerToken()) {
        //         return PublicShare::where('token', $request->bearerToken())->where('is_active', true)->first();
        //     }
        // });


        //         $this->app['auth']->viaRequest('api', function ($request) {
        //             return $request->session()->has('token') ? $request->session()->get('token') : null;
        //         });

        // $this->app['auth']->viaRequest('web', function ($request) {

        //     return User::find(1);

        //     if ($request->session('token')) {

        //     } else if ($request->input('api_token')) {

        //         return User::where('api_token', $request->input('api_token'))->first();

        //     } else if ($request->header('Authorization')) {

        //         //get the value of the authorization header
        //         $bearer = $request->header('Authorization');

        //         //split string into array ot two values
        //         $exploded_bearer = explode(" ", $bearer);

        //         //get the second element of the array which is the token
        //         $token = $exploded_bearer[1];

        //         //check if user with token exist
        //         return User::where('api_token', $token)->first();
        //     }
        // });



        // $this->app['auth']->viaRequest('api', function ($request) {
        //     dd('api', $request);
        // });


        // $this->app['auth']->viaRequest('api', function ($request) {
        //     dd($request);
        //     if ($request->input('api_token')) {
        //         return User::where('api_token', $request->input('api_token'))->first();
        //     }
        // });

        // $this->app['auth']->viaRequest('api', function ($request) {

        //     dd($request);
        //     // return $request->session()->has('token') ? $request->session()->get('token') : null;

        //     // if ($request->input('api_token')) {
        //     //     return User::where('api_token', $request->input('api_token'))->first();
        //     // }

        //     // if (isset($_SESSION['user'])){
        //     //     return $_SESSION['user'];
        //     // }

        // });


    }
}
