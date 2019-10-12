<?php

namespace onefasteuro\ShopifyApps;


use Illuminate\Support\ServiceProvider as BaseProvider;
use onefasteuro\ShopifyApps\Auth\ShopifyAuthService;
use onefasteuro\ShopifyApps\Auth\ShopifyVerifyOAuthRequest;
use onefasteuro\ShopifyApps\Http\SetupMiddleware;
use onefasteuro\ShopifyApps\Http\AuthMiddleware;
use onefasteuro\ShopifyApps\Http\SaveNonceMiddleware;
use onefasteuro\ShopifyApps\Http\Controllers\AuthController;
use Illuminate\Contracts\Events\Dispatcher as EventsDispatcher;

use onefasteuro\ShopifyApps\Contracts\BillingContract;

use onefasteuro\ShopifyApps\Http\Controllers\BillingController;
use onefasteuro\ShopifyClient\GraphClient;

class ShopifyAppsServiceProvider extends BaseProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }

        $this->loadRoutesFrom(__DIR__.'/routes.php');

        $this->loadMigrationsFrom(__DIR__.'/../migrations');
        
        $this->loadViewsFrom(__DIR__ . '/../views', 'shopifyapps');
        
		$this->loadEvents();
    }
    

	protected function loadEvents()
	{
		$events = $this->app['events'];
		
		//event if needed
		$events->listen(Events\TokenWasReceived::class, function(Events\TokenWasReceived $event){
			$token = $event->token;
		});

        //event if needed
		$events->listen(Events\AppWasCreated::class, function(Events\AppWasCreated $event){
            $model = $event->model;
        });

        //event if needed
		$events->listen(Events\AppWasSaved::class, function(Events\AppWasSaved $event){
            $model = $event->model;
        });

		$events->listen('router.matched', function($route, $request){
		   dd($route);
        });
	}




    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
	    $this->mergeConfigFrom(__DIR__ . '/../config/shopifyapps.php', 'shopifyapps');
	    
        $this->app->singleton(Nonce::class, function($app){
        	return new Nonce;
        });

	    $this->app->singleton(AuthController::class, function($app){
		    return new AuthController( $app[ShopifyAuthService::class] );
	    });

	    $this->registerMiddleware();


	    $this->registerAuthNamespace();

    }


    protected function registerMiddleware()
    {
        $this->app->singleton(AuthMiddleware::class,function($app){
            return new AuthMiddleware($app[Nonce::class]);
        });

        $this->app->singleton(SetupMiddleware::class, function($app){
            return new SetupMiddleware($app['config'], $app[Nonce::class]);
        });

        $this->app->singleton(SaveNonceMiddleware::class,function($app){
            return new SaveNonceMiddleware($app[Nonce::class]);
        });
    }



    protected function registerAuthNamespace()
    {
        $this->app->singleton(ShopifyAuthService::class, function($app){
            return new ShopifyAuthService(
                $app[Nonce::class],
                $app[GraphClient::class],
                $app[EventsDispatcher::class],
                $app['config']);
        });

        $this->app->singleton(ShopifyVerifyOAuthRequest::class, function($app) {
            $config = $app['config']->get('shopifyapps');
            return new ShopifyVerifyOAuthRequest($config, $app[Nonce::class]);
        });
    }



    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['shopifyapps'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__ . '/../config/shopifyapps.php' => config_path('shopifyapps.php'),
        ], 'shopifyapps.config');
	
	    $this->commands([

	    ]);
    }
}
