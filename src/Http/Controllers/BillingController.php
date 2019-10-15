<?php

namespace onefasteuro\ShopifyApps\Http\Controllers;


use Illuminate\Http\Request;

//exceptions
use onefasteuro\ShopifyApps\Repositories\AppRepositoryInterface;
use onefasteuro\ShopifyApps\Services\BillingService;
use onefasteuro\ShopifyClient\Exceptions\NotReadyException;
use onefasteuro\ShopifyClient\GraphClientInterface;


class BillingController extends BaseController
{
	
	public function __construct(BillingService $service)
	{
		parent::__construct($service);
	}
	
	/**
	 * Redirects the user to their shopify store to authorize or decline the charge
	 * @param $app_installation_id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function redirectToBill($app_installation_id)
	{
		$model = resolve(AppRepositoryInterface::class)->findByAppInstallId($app_installation_id);
		
		if(!$model) {
			//TODO
		}
		
		$client_params = [
			'token' => $model->token,
			'domain' => $model->shop_domain,
		];
		$client = resolve(GraphClientInterface::class, $client_params);
		
		$config = shopifyAppsConfig($model->app_id);
		
		$this->service->setAppConfig($config)->setAppDomain($model->shop_domain);
		
		try {
			$response = $this->service->authorizeCharge($client);
			
			//go to the authorization code
			return redirect()->to($response->body('confirmationUrl'));
		}
		catch(NotReadyException $e)
		{
			abort(403, $e->getMessage());
		}
	}
	
	
	public function recordCharge()
	{
	
	}
	
	
	public function handleWebhooks()
	{
	
	}
}