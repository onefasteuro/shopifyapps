<?php

namespace onefasteuro\ShopifyApps;


use Illuminate\Support\ServiceProvider;
use onefasteuro\ShopifyApps\Http\BillingController;
use Illuminate\Contracts\Events\Dispatcher as EventBus;
use onefasteuro\ShopifyClient\GraphClient;

class ShopifyBillingServiceProvider extends ServiceProvider
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
        
        $this->registerBillingProviders();
    }
	
	
	protected function registerBillingProviders()
	{
		
		$providers = $this->app['config']->get('shopifybilling');

		foreach($providers as $app_name => $p) {
			$c = $p['provider'];
			$provider = new $c($this->app[EventBus::class], $this->app[GraphClient::class], $p);
			
			$this->app[BillingRegistry::class]->register($app_name, $provider);
		}
	}
    


    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
	    $this->mergeConfigFrom(__DIR__ . '/../config/shopifybilling.php', 'shopifybilling');

	    
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
            __DIR__ . '/../config/shopifybilling.php' => config_path('shopifybilling.php'),
        ], 'shopifybilling.config');
	
	    $this->commands([

	    ]);
    }
}
