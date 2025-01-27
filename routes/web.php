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
| \Illuminate\Support\Facades\DB::enableQueryLog();
| var_export(\Illuminate\Support\Facades\DB::getQueryLog());
|--------------------------------------------------------------------------
*/

// Route::group(['domain' => 'whatever'], $callback);
// Route::group(['domain' => 'localhost'], function($route) {
//     Route::get('/', 'FrontendController@index')->name('index');
//     Route::get('/getTTRegions', 'RegionsController@getTTRegions');
//     Route::post('/get/cities', 'FrontendController@getCities')->name('get.cities');
// });

// Route::any('{myslug}/page/', array('as'=>'bar-page', 'uses'=>'Controllers\MyBar@index'))
//  ->where('myslug','^([0-9A-Za-z\-]+)?bar([0-9A-Za-z\-]+)?');

/*Route::domain('localhost:8000')->group(function () {
    Route::get('bla', 'FrontendController@macros')->name('bla');     //
});*/

//Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
//    return view('dashboard');
//})->name('dashboard');

// $router->group([
//     'prefix' => 'hxl',
//     'middleware' => ['auth:api', 'feature:hxl'],
//     'namespace' => 'HXL',
// ], function () use ($router) {
//     $router->get('/', 'HXLController@index');
//     $router->get('/licenses', 'HXLLicensesController@index');
//     $router->get('/tags', 'HXLTagsController@index');
//     $router->post('/metadata', 'HXLMetadataController@store');
//     $router->get('/metadata', 'HXLMetadataController@index');
//     $router->get('/organisations', 'HXLOrganisationsController@index');
// });
// $router->group(['middleware' => 'auth'], function () use ($router) {
//     $router->get('admin/index', [
//         'as' => 'admin.index', 'uses' => 'AdminController@index'
//     ]);
//     $router->post('admin/{id}/update', 'ArtworksController@update');
//     $router->post('admin/store', ['as' => 'createArtwork', 'uses' => 'ArtworksController@store']);
//     $router->get('admin', 'AdminController@index');
//     $router->get('admin/new', 'AdminController@new');
//     $router->get('admin/{id}/edit', 'AdminController@edit');
//     $router->get('admin/{id}/destroy', 'AdminController@destroy');
// });


$router->get('/test/auth-session', [
    'middleware' => ['auth:web'],
    function (\Laravel\Lumen\Routing\Router $router) {
        if (env('APP_DEBUG')) {
            return $router->app->version();
        }
    }
]);

$router->get('/test/auth-basic', [
    'middleware' => ['auth:api'],
    function (\Laravel\Lumen\Routing\Router $router) {
        if (env('APP_DEBUG')) {
            return $router->app->version();
        }
    }
]);

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
        return view("csrf", []);
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
            return redirect()->to('/test/csrf');
        }
    }
});