<?php

return [

    'shop_auth_url' => "https://%s.myshopify.com/admin/oauth/authorize?client_id=%s&scope=%s&state=%s&redirect_uri=%s",
    'redirect_url' => 'shopifyauth.auth.handle',
	'oauth_url' => 'https://%s/admin/oauth/access_token',
	
	'apps' => [
		'your app name' => [
			'client_id' => 'your app client id',
			'client_secret' => 'your app secret',
			'scope' => 'your app scope',
			'return_url' => 'https://%s/admin'
		]
	]
];