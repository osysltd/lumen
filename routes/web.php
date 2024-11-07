<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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

/*
|--------------------------------------------------------------------------
| Session and Cookies Support Tests
|--------------------------------------------------------------------------
*/

$router->get('/test/session-put', function (\Illuminate\Http\Request $request) {
    if (env('APP_DEBUG')) {
        $request->session()->put('name', config('app.name'));
        return response()->json([
            'session.name' => $request->session()->get('name'),
            'session.token' => Session::token()
        ]);
    }
});

$router->get('/test/session-get', function (\Illuminate\Http\Request $request) {
    if (env('APP_DEBUG')) {
        return response()->json([
            'session.name' => $request->session()->get('name'),
            'session.token' => Session::token()
        ]);
    }
});

$router->get('/test/cookie-set', function () {
    if (env('APP_DEBUG')) {
        $response = new Illuminate\Http\Response('Cookie name: name-of-cookie');
        $response->withCookie(
            new Symfony\Component\HttpFoundation\Cookie(
                'name-of-cookie',
                'value-of-cookie',
                '2147483647'
            )
        );
        return $response;
    }
});

$router->get('/test/cookie-get', function (\Illuminate\Http\Request $request) {
    if (env('APP_DEBUG')) {
        dd($request->cookie('name-of-cookie'));
    }
});

$router->get('/test/csrf', function (\Illuminate\Http\Request $request) {
    if (env('APP_DEBUG')) {
        return view("test", []);
    }
});

$router->post('/test/csrf-post', function (\Illuminate\Http\Request $request) {
    if (env('APP_DEBUG')) {
        try {
            $this->validate(
                $request,
                ['token' => 'required|string|max:40'],
                ['number' => 'nullable|numeric|digits:1']
            );
            Session::flash('message', implode(' ', [$request->token, $request->number, Session::token()]));

        } catch (\Illuminate\Validation\ValidationException $th) {
            Session::flash('message', json_encode($th->errors()));
        } finally {
            return redirect()->to('/test/csrf-get');
        }
    }
});