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
	protected $service;
	
	public function __construct(BillingService $service)
	{
		$this->service = $service;
	}
	
	public function redirectToBill($app_installation_id)
	{
		$model = resolve(AppRepositoryInterface::class)->findByAppInstallId($app_installation_id);
		
		$client_params = [
			'token' => $model->token,
			'domain' => $model->shop_domain,
		];
		$client = resolve(GraphClientInterface::class, $client_params);
		
		$config = static::getConfig($model->app_name);
		
		$this->service->setAppHandle($model->app_name)
			->setAppConfig($config)
			->setAppDomain($model->shop_domain);
		
		try {
			$response = $this->service->authorizeCharge($client);
		}
		catch(NotReadyException $e)
		{
			abort(403, $e->getMessage());
		}
		
		
		dd($response);
	}
	
	
	public function recordCharge()
	{
	
	}
}