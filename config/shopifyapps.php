<?php

return [
	
	'test-app' => [
            'client_id' => 'test-client_id',
            'client_secret' => 'test-client_secret',
            'scope' => 'test_scope',
            'return_url' => 'launch',
            'billing' => \onefasteuro\ShopifyApps\Billing\RecurringBilling::class,
	]
];