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
    \Illuminate\Support\Facades\Config::class => 'Config',
    \Illuminate\Support\Facades\Request::class => 'Request',
    \Illuminate\Support\Facades\Session::class => 'Session',
    \Illuminate\Support\Facades\Cookie::class => 'Cookie',
    \Illuminate\Support\Facades\Mail::class => 'Mail',
    \Illuminate\Support\Facades\Notification::class => 'Notification',
    \Illuminate\Support\Facades\Password::class => 'Password',

]);

$app->withEloquent();



$app->alias('session', \Illuminate\Session\SessionManager::class);
$app->alias('session.store', \Illuminate\Session\Store::class);
$app->alias('session.store', \Illuminate\Contracts\Session\Session::class);
$app->alias('cookie', \Illuminate\Cookie\CookieJar::class);
$app->alias('cookie', \Illuminate\Contracts\Cookie\Factory::class);
$app->alias('cookie', \Illuminate\Contracts\Cookie\QueueingFactory::class);

$app->alias('mailer', \Illuminate\Mail\Mailer::class);
$app->alias('mailer', \Illuminate\Contracts\Mail\Mailer::class);
$app->alias('mailer', \Illuminate\Contracts\Mail\MailQueue::class);

$app->alias('mail.manager', \Illuminate\Mail\MailManager::class);
$app->alias('mail.manager', \Illuminate\Contracts\Mail\Factory::class);

$app->alias('view', Illuminate\View\Factory::class);

$app->alias('auth.password', \Illuminate\Support\Facades\Password::class);
$app->alias('auth.password', \Illuminate\Auth\Passwords\PasswordBrokerManager::class);
$app->alias('auth.password', \Illuminate\Contracts\Auth\PasswordBrokerFactory::class);
$app->alias('auth.password.broker', \Illuminate\Auth\Passwords\PasswordBroker::class);
$app->alias('auth.password.broker',  \Illuminate\Contracts\Auth\PasswordBroker::class);

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
$app->configure('auth');
$app->configure('mail');
$app->configure('view');

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
| CSRF usage in forms: <input type="hidden" name="_token" value="{{ Session::token() }}" />
|
*/

$app->middleware([
    \Illuminate\Session\Middleware\StartSession::class,
    \Illuminate\View\Middleware\ShareErrorsFromSession::class,
    \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    \Illuminate\Cookie\Middleware\EncryptCookies::class,
    App\Http\Middleware\ConvertEmptyStringsToNull::class,
    App\Http\Middleware\TrimStrings::class,
    App\Http\Middleware\VerifyCsrfToken::class,
    App\Http\Middleware\TrustHosts::class,
    App\Http\Middleware\TrustProxies::class,
]);

$app->routeMiddleware([
    'auth' => App\Http\Middleware\Authenticate::class,
    'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
    'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
    'can' => \Illuminate\Auth\Middleware\Authorize::class,
    'guest' => App\Http\Middleware\RedirectIfAuthenticated::class,
    'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
    'signed' => App\Http\Middleware\ValidateSignature::class,
    'throttle' => App\Http\Middleware\ThrottleRequests::class,
    'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
]);

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

$app->register(\Illuminate\Session\SessionServiceProvider::class);
$app->register(\Illuminate\Cookie\CookieServiceProvider::class);
$app->register(App\Providers\AuthServiceProvider::class);
$app->register(App\Providers\SessionGuardServiceProvider::class);
$app->register(App\Providers\BasicGuardServiceProvider::class);
$app->register(\Illuminate\Mail\MailServiceProvider::class);
$app->register(\Illuminate\Notifications\NotificationServiceProvider::class);
$app->register(\Illuminate\Auth\Passwords\PasswordResetServiceProvider::class);

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