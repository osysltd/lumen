<?php

require_once __DIR__ . '/../vendor/autoload.php';

(
    new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
        dirname(__DIR__)
    )
)->bootstrap();

date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    dirname(__DIR__)
);

$app->withFacades(true, [
    \Illuminate\Support\Facades\Config::class => 'Config'
]);

$app->withEloquent();

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

/*
|--------------------------------------------------------------------------
| Register Config Files
|--------------------------------------------------------------------------
|
| Now we will register the "app" configuration file. If the file exists in
| your configuration directory it will be loaded; otherwise, we'll load
| the default version. You may register other files below as needed.
|
*/

$app->configure('app');
$app->configure('session');

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

// $app->middleware([
//     App\Http\Middleware\ExampleMiddleware::class
// ]);

// $app->routeMiddleware([
//     'auth' => App\Http\Middleware\Authenticate::class,
// ]);

/*
|--------------------------------------------------------------------------
| Session, Cookies, CSRF and Strings Normalization
|--------------------------------------------------------------------------
|
| Session Encryption is enabled in config/session.php by default.
| CSRF usage in forms: <input type="hidden" name="_token" value="{{ Session::token() }}" />
|
*/
// Set middleware.
$app->middleware([
    Illuminate\Session\Middleware\StartSession::class,
    Illuminate\Cookie\Middleware\EncryptCookies::class,
    Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    Illuminate\View\Middleware\ShareErrorsFromSession::class,
    App\Http\Middleware\ConvertEmptyStringsToNull::class,
    App\Http\Middleware\TrimStrings::class,
    App\Http\Middleware\VerifyCsrfToken::class,
]);

// Set cookie, Session service provider.
$app->alias('session', \Illuminate\Session\SessionManager::class);
$app->alias('session.store', \Illuminate\Session\Store::class);
$app->alias('session.store', \Illuminate\Contracts\Session\Session::class);
$app->alias('cookie', \Illuminate\Cookie\CookieJar::class);
$app->alias('cookie', \Illuminate\Contracts\Cookie\Factory::class);
$app->alias('cookie', \Illuminate\Contracts\Cookie\QueueingFactory::class);
$app->bind('Illuminate\Contracts\Cookie\QueueingFactory', 'cookie');
$app->register(\Illuminate\Cookie\CookieServiceProvider::class);
$app->register(\Illuminate\Session\SessionServiceProvider::class);
class_alias(Illuminate\Support\Facades\Session::class, 'Session');
class_alias(Illuminate\Support\Facades\Cookie::class, 'Cookie');
class_alias(Illuminate\Support\Facades\Request::class, 'Request');

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

// $app->register(App\Providers\AppServiceProvider::class);
// $app->register(App\Providers\AuthServiceProvider::class);
// $app->register(App\Providers\EventServiceProvider::class);

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

$app->router->group([
    'namespace' => 'App\Http\Controllers',
], function ($router) {
    require __DIR__ . '/../routes/web.php';
});

return $app;