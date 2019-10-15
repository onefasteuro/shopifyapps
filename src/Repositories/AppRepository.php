<?php

namespace onefasteuro\ShopifyApps\Repositories;

use onefasteuro\ShopifyApps\Models\ShopifyApp;
use onefasteuro\ShopifyUtils\ShopifyUtils;

class AppRepository implements AppRepositoryInterface
{
	
	
	
	public function model()
	{
		return ShopifyApp::class;
	}
	
	public function findByAppInstallId($app_id)
	{
		$app_id = ShopifyUtils::gidParse($app_id);
		return ShopifyApp::where('app_installation_id', '=', $app_id)->first();
	}
	
	
	/**
	 * Create a new shopify app instance in our data storage
	 * @param $app_name
	 * @param $token
	 * @param array $app_data
	 * @param array $shop_data
	 * @return mixed
	 */
	public function create($app_name, $token, array $app_data, array $shop_data)
	{
		$m = $this->findByAppInstallId($app_data['id']);
		if(!$m) {
			$model = $this->model();
			$m = new $model();
			unset($model);
		}
		
		$m->app_installation_id = ShopifyUtils::gidParse($app_data['id']);
		$m->app_name = $app_name;
		$m->app_id = $app_data['id'];
		$m->app_launch_url = $app_data['launchUrl'];
		$m->shop_domain = $shop_data['domain'];
		$m->shop_name = $shop_data['name'];
		$m->shop_email = $shop_data['email'];
		$m->shop_id = $shop_data['id'];
		$m->token = $token;
		$m->save();
		
		return $m;
	}
	
	
}
