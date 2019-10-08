<?php

return [

    'shop_auth_url' => "https://%s.myshopify.com/admin/oauth/authorize?client_id=%s&scope=%s&state=%s&redirect_uri=%s",
    'redirect_url' => 'shopifyauth.auth.handle',
	'oauth_url' => 'https://%s/admin/oauth/access_token',
	
	'apps' => [
		'bridge' => [
			'client_id' => 'eb861be684c44f75e8f3081b16fb5c1d',
			'client_secret' => '3d2c4ede12abe9516deb59828ea2a668',
			'scope' => 'read_products,write_products,read_orders,read_inventory,write_inventory,read_locations',
			'return_url' => 'https://%s/admin'
		]
	]
];