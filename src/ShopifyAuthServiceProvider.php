<?php

namespace onefasteuro\ShopifyAuth;


use Illuminate\Support\ServiceProvider;
use onefasteuro\ShopifyAuth\Http\AuthMiddleware;
use onefasteuro\ShopifyAuth\Http\NonceMiddleware;
use onefasteuro\ShopifyAuth\Http\Controllers\AuthController;
use Illuminate\Contracts\Events\Dispatcher as EventBus;

class ShopifyAuthServiceProvider extends ServiceProvider
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
        
        $this->loadViewsFrom(__DIR__ . '/../views', 'shopifyauth');
    }








    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
	    $this->mergeConfigFrom(__DIR__ . '/../config/shopifyauth.php', 'shopifyauth');
	    
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
		    return new AuthController( $app[Nonce::class], $app[\onefasteuro\ShopifyClient\GraphClient::class], $app[EventBus::class], $app[Helpers::class]);
	    });
	    
	    $this->app->singleton(Helpers::class, function($app){
	    	return new Helpers($app['config']->get('shopifyauth'), $app[Nonce::class]);
	    });
	    
    }
    

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['shopifyauth'];
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
            __DIR__ . '/../config/shopifyauth.php' => config_path('shopifyauth.php'),
        ], 'shopifyauth.config');
	
	    $this->commands([

	    ]);
    }
}
