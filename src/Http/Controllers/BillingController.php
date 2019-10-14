<?php

namespace onefasteuro\ShopifyApps\Http\Controllers;


use Illuminate\Http\Request;

//exceptions
use onefasteuro\ShopifyApps\Repositories\AppRepositoryInterface;
use onefasteuro\ShopifyApps\Repositories\GraphqlRepository;
use onefasteuro\ShopifyApps\Services\BillingService;


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
		
		$params = [
			'token' => $model->token,
			'domain' => $model->shop_domain,
		];
		
		$config = static::getConfig($model->app_name);
		
		$this->service->setAppHandle($model->app_name)
			->setAppConfig($config)
			->setAppDomain($model->shop_domain);
		
		
	}
}