<?php

return [
	
	
	'app-handle' => [
            'client_id' => 'test-client_id',
            'client_secret' => 'test-client_secret',
            'scope' => 'test_scope',
            'redirect_url' => 'shopify.auth.complete',
            'return_url' => 'launch',
            'billing' => [
            	'trial' => 0,
	            'test' => true,
	            'redirect_url' => '',
	            'type' => 'recurring',
	            'plans' => [
	            	''
	            ]
            ],
	]
];