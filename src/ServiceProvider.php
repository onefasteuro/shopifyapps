<?php

namespace onefasteuro\ShopifyApps;


use Illuminate\Support\ServiceProvider as BaseProvider;
use onefasteuro\ShopifyApps\Http\AuthMiddleware;
use onefasteuro\ShopifyApps\Http\NonceMiddleware;
use onefasteuro\ShopifyApps\Http\Controllers\AuthController;
use Illuminate\Contracts\Events\Dispatcher as EventBus;
use Illuminate\Support\Facades\Event;


use onefasteuro\ShopifyApps\Http\Controllers\BillingController;

class ServiceProvider extends BaseProvider
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
		
		//$this->loadBillingProviders();
    }
	
	protected function loadBillingProviders()
	{
		
		$providers = $this->app['config']->get('shopifybilling');
		
		foreach($providers as $app_name => $p) {
			$c = $p['provider'];
			$provider = new $c($this->app[EventBus::class], $this->app[GraphClient::class], $p);
			
			$this->app[BillingRegistry::class]->register($app_name, $provider);
		}
	}



	protected function loadEvents()
	{
		//event if needed
		Event::listen(Events\TokenWasReceived::class, function(Events\TokenWasReceived $event){
			$token = $event->token;
		});

        //event if needed
        Event::listen(Events\AppWasCreated::class, function(Events\AppWasCreated $event){
            $model = $event->model;
        });

        //event if needed
        Event::listen(Events\AppWasSaved::class, function(Events\AppWasSaved $event){
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
        
        $this->app->singleton(AuthMiddleware::class,function($app){
        	return new AuthMiddleware($app[Nonce::class]);
        });
	
	    $this->app->singleton(NonceMiddleware::class,function($app){
		    return new NonceMiddleware($app[Nonce::class]);
	    });
	
	    $this->app->singleton(AuthController::class, function($app){
		    return new AuthController( $app[Nonce::class], $app[\onefasteuro\ShopifyClient\GraphClient::class], $app[EventBus::class]);
	    });
	    
	    
	    //billing
	    $this->app->singleton(BillingRegistry::class, function($app){
		    return new BillingRegistry;
	    });
	
	    $this->app->singleton(BillingController::class, function($app) {
		    return new BillingController($app[EventBus::class], $app[BillingRegistry::class]);
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
