<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Models\User;
use App\Http\Controllers\AuthController;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('home', ['as' => 'home', 'uses' => 'AuthController@home']);
$router->get('profile', ['as' => 'profile', 'uses' => 'AuthController@profile']);

$router->get('login', ['as' => 'login', 'middleware' => 'guest', 'uses' => 'AuthController@index']);
$router->get('register', ['as' => 'register', 'middleware' => 'guest', 'uses' => 'AuthController@register']);
$router->get('recovery', ['as' => 'recovery', 'middleware' => 'guest', 'uses' => 'AuthController@recovery']);

$router->get('logout', ['as' => 'logout', 'uses' => 'AuthController@logout']);

$router->post('dologin', ['as' => 'login.do', 'middleware' => 'guest', 'uses' => 'AuthController@doLogin']);
$router->post('doregistration', ['as' => 'registration.do', 'middleware' => 'guest', 'uses' => 'AuthController@doRegistration']);
$router->post('dorecovery', ['as' => 'recovery.do', 'middleware' => 'guest', 'uses' => 'AuthController@doRecovery']);


/*
|--------------------------------------------------------------------------
| Session and Cookies Support Tests
| \Illuminate\Support\Facades\DB::enableQueryLog();
| var_export(\Illuminate\Support\Facades\DB::getQueryLog());
|--------------------------------------------------------------------------
*/

if (env('APP_DEBUG')) {
    $router->group([
        'prefix' => 'test',
        // ['domain' => 'localhost'],
        // 'middleware' => ['auth:api', 'auth:session'],
        // 'namespace' => 'namespace',
    ], function () use ($router) {

        $router->get('cookie-set', function () {
            $response = new Illuminate\Http\Response('Cookie name: name-of-cookie');
            $response->withCookie(
                new Symfony\Component\HttpFoundation\Cookie(
                    'name-of-cookie',
                    'value-of-cookie',
                    '2147483647'
                )
            );
            return $response;
        });

        $router->get('cookie-get', function (\Illuminate\Http\Request $request) {
            dd($request->cookie('name-of-cookie'));
        });

        $router->get('session-put', function (\Illuminate\Http\Request $request) {
            $request->session()->put('name', config('app.name'));
            return response()->json([
                'session.name' => $request->session()->get('name'),
                'session.token' => Session::token()
            ]);
        });

        $router->get('session-get', function (\Illuminate\Http\Request $request) {
            return response()->json([
                'session.name' => $request->session()->get('name'),
                'session.token' => Session::token()
            ]);
        });

        $router->get('csrf-form', function (\Illuminate\Http\Request $request) {
            return view("csrf", []);
        });

        $router->post('csrf-post', function (\Illuminate\Http\Request $request) {
            try {
                $this->validate(
                    $request,
                    [
                        'token' => 'required|string|max:40',
                        'number' => 'nullable|numeric|digits:1'
                    ]
                );
                Session::flash('message', implode(' ', [$request->token, $request->number, Session::token()]));

            } catch (\Illuminate\Validation\ValidationException $th) {
                Session::flash('message', value: json_encode($th->errors()));
            } finally {
                return redirect()->to('/test/csrf-form');
            }

        });

        $router->get('login', function () use ($router) {
            $user = User::create([
                'name' => 'Name',
                'email' => 'Email',
                'password' => Hash::make('password')
            ]);
            Auth::login($user);
            return $router->app->version();
        });

        $router->get('logout', function () use ($router) {
            Session::flush();
            Auth::logout();
            return $router->app->version();
        });

        $router->get('auth-web-session', [
            'middleware' => ['auth:web'],
            function (\Laravel\Lumen\Routing\Router $router) {
                dd(Auth::user());
            }
        ]);

        $router->get('auth-api-basic', [
            'middleware' => ['auth:api'],
            function (\Laravel\Lumen\Routing\Router $router) {
                return $router->app->version();
            }
        ]);

        $router->get('index', [
            'as' => 'test.index',
            'uses' => 'TestController@index'
        ]);

    });

}
;
