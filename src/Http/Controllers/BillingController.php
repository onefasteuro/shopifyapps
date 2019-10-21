<?php

namespace onefasteuro\ShopifyApps\Http\Controllers;



use onefasteuro\ShopifyApps\Services\BillingService;


class BillingController extends AbstractBaseController
{

	public function redirectToBill($app_handle, $app_installation_id)
	{
		$shopify_app = $this->repository->findByInstallationId($app_installation_id);
		
		
		//the Http client
		$client = $this->getHttpClient($shopify_app->shop_domain, $shopify_app->token);

		
		$launch_call = '{
    	    install: appInstallation {
    	        launchUrl
    	    }
    	}';
		
		$launch_url_response = $client->query($launch_call);
		
		$config = $this->config->get("shopifyapps.$app_handle.billing");
		$config['return_url'] = $launch_url_response->getBody('data.install.launchUrl');
		
		
		$response = BillingService::authorizeCharge($client, $config);
		
		//go to the authorization code
		return redirect()->to($response->data('bill.confirmationUrl'));
	}
	
	
	
	public function handleWebhooks()
	{
	
	}
}