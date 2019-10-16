<?php

namespace onefasteuro\ShopifyApps;

use Illuminate\Support\ServiceProvider;

use onefasteuro\ShopifyApps\Services\BillingService;
use onefasteuro\ShopifyApps\Services\AuthService;
use onefasteuro\ShopifyApps\Services\OAuthRequestValidator;


use onefasteuro\ShopifyApps\Http\Controllers\BillingController;

//middleware
use onefasteuro\ShopifyApps\Http\SetNonceStoreMiddleware;
use onefasteuro\ShopifyApps\Http\AuthMiddleware;
use onefasteuro\ShopifyApps\Http\SaveNonceStoreMiddleware;

//repositories
use onefasteuro\ShopifyApps\Repositories\AppRepository;
use onefasteuro\ShopifyApps\Repositories\AppRepositoryInterface;

//controllers
use onefasteuro\ShopifyApps\Http\Controllers\AuthController;


class ShopifyAppsServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
    	require_once __DIR__.'/helpers.php';
    	
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
        
	
	    $this->app->bind(AppRepositoryInterface::class, function(){
		    return new AppRepository;
	    });
	
	    $this->app->singleton(AuthService::class, function($app, $params = []){
		
	    	$service = new AuthService($app[Nonce::class], $app['events']);
	    	
		    if(array_key_exists('config', $params)) {
			    $config = $params['config'];
			    $service->setAppConfig($config);
		    }
	    	
		    return $service;
	    });
	    
	    
	    $this->app->singleton(BillingService::class, function($app, $params = []){
		
		    $service = new BillingService($app['events']);
		
		    if(array_key_exists('config', $params)) {
			    $config = $params['config'];
			    $service->setAppConfig($config);
		    }
		    
		    return $service;
	    });
	    
	    $this->app->singleton(OAuthRequestValidator::class, function($app) {
		    $config = $app['config']->get('shopifyapps');
		    return new OAuthRequestValidator($config, $app[Nonce::class]);
	    });
	
	
	    //middleware
		$this->registerMiddleware();
	
		$this->registerControllers();

    }
    
    protected function registerMiddleware()
    {
	    $this->app->singleton(AuthMiddleware::class,function($app){
		    return new AuthMiddleware($app[Nonce::class]);
	    });
	
	    $this->app->singleton(SetNonceStoreMiddleware::class, function($app){
		    return new SetNonceStoreMiddleware($app[Nonce::class]);
	    });
	
	    $this->app->singleton(SaveNonceStoreMiddleware::class,function($app){
		    return new SaveNonceStoreMiddleware($app[Nonce::class]);
	    });
    }
    
    protected function registerControllers()
    {
	    $this->app->singleton(AuthController::class, function($app){
		    return new AuthController( $app[AppRepositoryInterface::class] );
	    });
	
	    $this->app->singleton(BillingController::class, function($app){
		    return new BillingController( $app[AppRepositoryInterface::class] );
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
