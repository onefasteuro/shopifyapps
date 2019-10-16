<?php

namespace onefasteuro\ShopifyApps\Http\Controllers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\App;

use onefasteuro\ShopifyApps\Services\BillingService;
use onefasteuro\ShopifyClient\Exceptions\NotReadyException;
use onefasteuro\ShopifyClient\GraphClientInterface;


class BillingController extends AbstractBaseController
{
	protected function getService($app_id)
	{
		//get the right config file
		$config = Config::get("shopifyapps.app_$app_id");
		return App::makeWith(BillingService::class, ['config' => $config]);
	}
	
	protected function init()
	{
	
	}
	
	/**
	 * Redirects the user to their shopify store to authorize or decline the charge
	 * @param $app_installation_id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function redirectToBill($app_installation_id)
	{
		$model = $this->repository->findByAppInstallId($app_installation_id);
		
		if(!$model) {
			//TODO
		}
		
		$service = $this->getService($model->app_id);
		
		$client = resolve(GraphClientInterface::class, [
			'token' => $model->token,
			'domain' => $model->shop_domain,
		]);
		
		$response = $service->authorizeCharge($client);
		
		//go to the authorization code
		return redirect()->to($response->body('data.bill.confirmationUrl'));
	}
	
	
	
	public function completeBilling($app_installation_id)
	{
		$model = $this->repository->findByAppInstallId($app_installation_id);
		
		if(!$model) {
			//TODO
		}
	}
	
	
	public function handleWebhooks()
	{
	
	}
}