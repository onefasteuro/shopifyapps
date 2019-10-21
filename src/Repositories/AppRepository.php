<?php

namespace onefasteuro\ShopifyApps\Repositories;

use onefasteuro\ShopifyApps\Models\ShopifyApp;
use onefasteuro\ShopifyUtils\ShopifyUtils;
use Illuminate\Support\Arr;

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
	
	public function findByToken($tkn)
	{
		return call_user_func_array([$this->model(), 'where'], ['token', '=', $tkn])->first();
	}


	/**
	 * Create a new shopify app instance in our data storage
	 * @param $app_name
	 * @param $token
	 * @param array $app_data
	 * @param array $shop_data
	 * @return mixed
	 */
	public function create($handle, $token, $scope)
	{
		$m = $this->findByToken($token);
		if(!$m) {
			$model = $this->model();
			$m = new $model();
			unset($model);
		}
		
		$m->app_handle = $handle;
		$m->token = $token;
		$m->scope = $scope;
		$m->save();
		
		return $m;
	}
	
	
}
