<?php

namespace onefasteuro\ShopifyApps\Tests;

use onefasteuro\Shopify\ShopifyServiceProvider;
use onefasteuro\ShopifyApps\ServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use PDO;
	
class TestCase extends BaseTestCase
{
		
	protected function getPackageProviders($app)
	{
		return [ServiceProvider::class];
	}


    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {


    }
}
