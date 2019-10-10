<?php

return [
	
	'test-app' => [
		'client_id' => 'test-client_id',
		'client_secret' => 'test-client_secret',
		'scope' => 'test_scope',
		'return_url' => 'launch',
		'billing' => [
			'test' => true,
			'trial' => false,
			'name' => 'testname',
			'complete_url' => 'shopify.billing.complete',
			'return_url' => 'shop-apps',
			'provider' => \onefasteuro\ShopifyApps\Billing\RecurringBilling::class
		]
	]
];