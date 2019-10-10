<?php

return [
	
	'test-app' => [
		'client_id' => 'test-client_id',
		'client_secret' => 'test-client_secret',
		'scope' => 'test_scope',
		'return_url' => 'launch',
		'billing' => [
			'test' => true,
			'trial' => 0,
			'name' => 'testname',
			'return_url' => 'shopifybilling.complete',
			'return_to' => 'shop-apps',
			'provider' => \onefasteuro\ShopifyApps\Billing\RecurringBilling::class
		]
	]
];