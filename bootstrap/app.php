<?php

require_once __DIR__.'/../vendor/autoload.php';

//Custom Helper
require_once __DIR__.'/../helpers/basic.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
    dirname(__DIR__)
))->bootstrap();

date_default_timezone_set(env('APP_TIMEZONE', 'Asia/Jakarta'));

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

// $app->withFacades();



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

$app->configure('upload');

$app->configure('databases');

$app->configure('googlemaps');

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

$app->middleware([
     App\Http\Middleware\CorsMiddleware::class
]);

$app->routeMiddleware([
    'auth' => App\Http\Middleware\TokenMiddleware::class,
    'auth_jagomart' => App\Http\Middleware\JagomartAuth::class,
    'auth_jagoride' => App\Http\Middleware\JagorideAuth::class,
    'auth_jagofood' => App\Http\Middleware\JagofoodAuth::class,
    'auth_customer' => App\Http\Middleware\CustomerMiddleware::class,
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
class_alias(GoogleMaps\ServiceProvider\GoogleMapsServiceProvider::class, 'GoogleMaps');
$app->withFacades();

// $app->register(GoogleMaps\ServiceProvider\GoogleMapsServiceProvider::class);
// $app->withFacades(true, [GoogleMaps\Facade\GoogleMapsFacade::class => 'GoogleMaps']);

$app->register(\Thedevsaddam\LumenRouteList\LumenRouteListServiceProvider::class);
$app->register(App\Providers\CatchAllOptionsRequestProvider::class);
$app->withEloquent();
// $app->register(App\Providers\AppServiceProvider::class);
// $app->register(App\Providers\AuthServiceProvider::class);
// $app->register(App\Providers\EventServiceProvider::class);

// Custom Auth Service
$app->bind(
    App\Services\Auth\AuthServiceContract::class,
    App\Services\Auth\AuthService::class
);

//Register Validation Service
$app->bind(
  App\Services\Jagomart\JagomartRequest::class,
  App\Services\Jagomart\JagomartRequestService::class
);

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
    require __DIR__.'/../routes/web.php';
});

$app->router->group([
    'namespace' => 'App\Http\Controllers\Admin',
], function ($router) {
    require __DIR__.'/../routes/admin.php';
});

$app->router->group([
    'namespace' => 'App\Http\Controllers\Customer',
], function ($router) {
    require __DIR__.'/../routes/customer.php';
});

$app->router->group([
    'namespace' => 'App\Http\Controllers\Jagomart',
], function ($router) {
    require __DIR__.'/../routes/jagomart.php';
});

$app->router->group([
    'namespace' => 'App\Http\Controllers\Jagofood',
], function ($router) {
    require __DIR__.'/../routes/jagofood.php';
});

$app->router->group([
    'namespace' => 'App\Http\Controllers\Driver',
], function ($router) {
    require __DIR__.'/../routes/jagoride.php';
});

return $app;
