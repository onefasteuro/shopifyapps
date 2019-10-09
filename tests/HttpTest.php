<?php
	
namespace onefasteuro\ShopifyAuth\Tests;


	
use onefasteuro\ShopifyAuth\Helpers;

class HttpTest extends TestCase
{


	public function testGid()
	{
		$id = 'gid://shopify/AppInstallation/193861025843';
		$gid = Helpers::gidParse($id);

		$this->assertIsInt($gid);
	}

	
}