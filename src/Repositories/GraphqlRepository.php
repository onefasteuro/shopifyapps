<?php

namespace onefasteuro\ShopifyApps\Repositories;


use onefasteuro\ShopifyClient\GraphClient;

class GraphqlRepository
{
	protected $client;
	
	public function __construct(GraphClient $client, $domain, $token)
	{
		$this->client = $client;
		$this->client->init($domain, $token);
	}
	
	
	
	public function getShopInfo()
	{
		$call = 'query {
			  app: appInstallation {
			    id
			    launchUrl
			    uninstallUrl
			    current: app {
			        id
			        handle
			    }
			  }
			  shop {
			    id
			    name
			    email
			    domain: myshopifyDomain
			  }
			}';
		
		return  $this->client->query($call, []);
	}
}
