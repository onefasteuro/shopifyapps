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
	
	public function set($key, $url)
	{
		$this->config[$key] =  $url;
		return $this;
	}
	
	abstract public function bill();
	
	public function getName()
	{
		return $this->config['name'];
	}
	
	public function getTest()
	{
		return $this->config['test'];
	}
	
	public function getCompletedUrl()
	{
		$this->config['completed_url'];
	}
	
	public function getReturnUrl()
	{
		return route($this->config['return_url'], ['id' => $this->shopify_app_id]);
	}
	
	abstract public function parseResponse(array $response);
	
}