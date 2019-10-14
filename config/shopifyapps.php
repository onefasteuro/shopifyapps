<?php

return [
	
	'test-app' => [
            'client_id' => 'test-client_id',
            'client_secret' => 'test-client_secret',
            'scope' => 'test_scope',
            'redirect_url' => 'shopify.auth.complete',
            'return_url' => 'launch',
            'billing' => \onefasteuro\ShopifyApps\Billing\RecurringBilling::class,
	]
];