<?php

return [
	
	'your app name' => [
		'client_id' => 'your app client id',
		'client_secret' => 'your app secret',
		'scope' => 'your app scope',
		'return_url' => 'https://%s/admin',
		'billing' => [
			'test' => true,
			'trial' => 0,
			'name' => 'Sync Inventory with GP',
			'return_url' => 'shopifybilling.complete',
			'return_to' => 'shop-apps',
			'provider' => \onefasteuro\ShopifyApps\Billing\RecurringBilling::class
		]
	]
];