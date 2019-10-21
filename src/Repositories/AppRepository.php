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
	
	public function find($id)
	{
		return call_user_func_array([$this->model(), 'find'], [$id]);
	}
	
	public function findByInstallationId($id)
	{
		$id = ShopifyUtils::gidParse($id);
		return call_user_func_array([$this->model(), 'where'], ['app_installation_id', '=', $id])->first();
	}


	/**
	 * Create a new shopify app instance in our data storage
	 * @param $app_name
	 * @param $token
	 * @param array $app_data
	 * @param array $shop_data
	 * @return mixed
	 */
	public function create($token, $app_id, $app_installation_id, $domain)
	{
		$m = $this->findByInstallationId($app_installation_id);
		if(!$m) {
			$model = $this->model();
			$m = new $model();
			unset($model);
		}
		
		$m->shop_domain = ShopifyUtils::formatFqdn($domain);
		$m->app_installation_id = ShopifyUtils::gidParse($app_installation_id);
		$m->app_id = ShopifyUtils::gidParse($app_id);
		$m->token = $token;
		$m->save();
		
		return $m;
	}

	
}
