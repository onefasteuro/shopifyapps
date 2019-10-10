<?php
	
namespace onefasteuro\ShopifyApps\Tests;

use onefasteuro\ShopifyApps\Models\ShopifyApp;

class HttpTest extends TestCase
{


	public function testGid()
	{
		$model = new ShopifyApp;
		$model->app_installation_id = 'gid://shopify/AppInstallation/193861022222';
		$model->shop_id = 'gid://shopify/Shop/193861022222';
		
		$this->assertIsInt($model->shop_id);
		$this->assertIsInt($model->app_installation_id);
		$this->assertIsString($model->shop_gid);
		$this->assertIsString($model->app_installation_gid);
		
		$this->assertStringContainsString('Shop', $model->shop_gid);
		$this->assertStringContainsString('AppInstallation', $model->app_installation_gid);
	}

	
}