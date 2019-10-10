<?php

namespace onefasteuro\ShopifyApps\Billing;

//models
use onefasteuro\ShopifyApps\Contracts\BillingContract;


use onefasteuro\ShopifyClient\GraphClient;
use Illuminate\Contracts\Events\Dispatcher as EventBus;

abstract class BaseBilling implements BillingContract
{
	protected $client;
	protected $config;
	protected $events;
	protected $shopify_app_id;
	
	public function __construct(EventBus $events, GraphClient $client,  array $config)
	{
		$this->events = $events;
		$this->client = $client;
		$this->config = $config;
	}
	
	public function init($domain, $token, $id)
	{
		$this->client->init($domain, $token);
		$this->shopify_app_id = $id;
		return $this;
	}
	
	abstract public function bill(\onefasteuro\ShopifyApps\Contracts\ModelContract $model);
	
	abstract public function parseResponse(array $response);
	
}