<?php

namespace onefasteuro\ShopifyAuth\Tests;

use onefasteuro\Shopify\ShopifyServiceProvider;
use onefasteuro\ShopifyAuth\ShopifyAuthServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use PDO;
	
class TestCase extends BaseTestCase
{
		
	protected function getPackageProviders($app)
	{
		return [ShopifyAuthServiceProvider::class];
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
