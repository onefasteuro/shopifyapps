<?php

namespace onefasteuro\ShopifyApps;


use Illuminate\Support\ServiceProvider;
use onefasteuro\ShopifyApps\Services\BillingService;


class ShopifyAppsServiceProvider extends ServiceProvider
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
        
	    $this->app->bind(Repositories\AppRepositoryInterface::class, function(){
		    return new Repositories\AppRepository;
	    });
	    
	    $this->app->bind(Services\BillingService::class, function($app, $params){
	    	return new BillingService($params['client'], $params['config']);
	    });
	
	    //middleware
		$this->registerMiddleware();
	
		$this->registerControllers();

    }
    
    protected function registerMiddleware()
    {
	    $this->app->singleton(Http\Middlewares\AuthMiddleware::class,function($app){
		    return new Http\Middlewares\AuthMiddleware($app[Nonce::class]);
	    });
        $this->app->alias(Http\Middlewares\AuthMiddleware::class, 'shopifyapps.middleware.auth');
	
	    $this->app->singleton(Http\Middlewares\SetNonceStoreMiddleware::class, function($app){
		    return new Http\Middlewares\SetNonceStoreMiddleware($app[Nonce::class]);
	    });
        $this->app->alias(Http\Middlewares\SetNonceStoreMiddleware::class, 'shopifyapps.middleware.setnonce');
	
	    $this->app->singleton(Http\Middlewares\SaveNonceStoreMiddleware::class,function($app){
		    return new Http\Middlewares\SaveNonceStoreMiddleware($app[Nonce::class]);
	    });
        $this->app->alias(Http\Middlewares\SaveNonceStoreMiddleware::class, 'shopifyapps.middleware.savenonce');
    }
    
    protected function registerControllers()
    {
	    $this->app->singleton(Http\Controllers\AuthController::class, function($app){
		    return new Http\Controllers\AuthController($app['config'], $app[Repositories\AppRepositoryInterface::class], $app[Nonce::class] );
	    });
	
	    $this->app->singleton(Http\Controllers\BillingController::class, function($app){
		    return new Http\Controllers\BillingController($app['config'], $app[Repositories\AppRepositoryInterface::class]);
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
