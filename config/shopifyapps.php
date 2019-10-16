<?php

return [
	
	'default' => 'app_appid',
	
	'app_appid' => [
			'app_id' => 'app_id',
            'client_id' => 'test-client_id',
            'client_secret' => 'test-client_secret',
            'scope' => 'test_scope',
            'redirect_url' => 'shopify.auth.complete',
            'return_url' => 'launch',
            'billing' => [
	            'trial' => 0,
	            'test' => true,
	            'return_url' => 'https://bpisports.com',
	            'redirect_url' => '',
	            'type' => 'recurring',
	            'plans' => [
		            [
		            	'amount' => 500,
		                'currency' => 'USD'
			        ],
	            ]
            ],
	]
];