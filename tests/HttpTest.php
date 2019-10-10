<?php
	
namespace onefasteuro\ShopifyApps\Tests;

use onefasteuro\ShopifyApps\Models\ShopifyApp;
use onefasteuro\ShopifyApps\Nonce;

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
	
	//test the redirect
	public function testRedirectToAuth()
	{
		$response = $this->get('/shopify/auth/test-app/mydomain');
		$response->assertStatus(302);
	}
	
	//test our auth url
	public function testAuthUrl()
	{
		$response = $this->get('/shopify/auth/test-app/mydomain/url');
		$this->assertStringContainsString('https://mydomain.myshopify.com/admin/oauth/authorize?client_id=test-client_id&scope=test_scope&state=' .	$nonce = app(Nonce::class)->retrieve() . '&redirect_uri=' . route('shopify.auth.handle', ['appname' => 'test-app']), $response->getContent());
	}
}