<?php

namespace onefasteuro\ShopifyAuth;


use Illuminate\Database\Eloquent\Model;

class ShopifyApp extends Model
{
	protected $table = 'shopify_apps';
	
	//appends to the toArray output
	protected $appends = ['shopify_shop_id'];
	
	/**
	 * @param string $domain
	 * @param string $handle
	 * @param string $id
	 * @return mixed
	 */
	public static function findInstallation(string $domain, string $handle, string $id)
	{
		return static::where('shop_domain', '=', $domain)
			->where('app_name', '=', $handle)
			->where('app_installation_id', '=', $id)->first();
	}
}