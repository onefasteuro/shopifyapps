<?php

namespace onefasteuro\ShopifyApps\Repositories;

use onefasteuro\ShopifyApps\Models\ShopifyApp;
use onefasteuro\ShopifyUtils\ShopifyUtils;
use onefasteuro\ShopifyApps\Contracts\AppRepositoryInterface;

class AppRepository implements AppRepositoryInterface
{
	public function model()
	{
		return ShopifyApp::class;
	}
	
	public function findByAppId($app_id)
	{
		$app_id = ShopifyUtils::gidParse($app_id);
		return ShopifyApp::where('app_installation_id', '=', $app_id)->first();
	}
	
	/**
	 * Create a new instance of an app
	 * @param array $app_data
	 * @param array $shop_data
	 */
	public function create($token, array $app_data, array $shop_data)
	{
		$m = $this->findByAppId($app_data['id']);
		if(!$m) {
			$m = new ($this->model())();
		}
		
		$m->app_installation_id = ShopifyUtils::gidParse($app_data['id']);
		$m->app_name = '';
		$m->app_launch_url = $app_data['launchUrl'];
		$m->shop_domain = $shop_data['domain'];
		$m->shop_name = $shop_data['name'];
		$m->shop_email = $shop_data['email'];
		$m->token = $token;
		$m->save();
		
		return $m;
		
	}
}
