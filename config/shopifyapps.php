<?php

use onefasteuro\ShopifyApps\Services\BillingService;


return [
	

	
	'appname' => [
		'client_id' => 'YOUR CLIENT ID',
		'client_secret' => 'YOUR CLIENT SECRET',
		'scope' => 'scope',
		'billing' => [
			'trial' => 0,
			'test' => true,
			'name' => 'GP Inventory Bridge plan',
			'type' => BillingService::BILLING_SUBSCRIPTION,
			'plans' => [
				array('amount' => 500, 'currency' => 'USD')
			]
		],
	]
];