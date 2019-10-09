<?php

namespace onefasteuro\ShopifyAuth;


use Illuminate\Database\Eloquent\Model;

class ShopifyApp extends Model
{
	protected $table = 'shopify_apps';
	
	//appends to the toArray output
	protected $appends = ['shopify_shop_id'];
	
	public function getShopNameAttribute()
	{
		$exploded = explode('.', $this->shop);
		return count($exploded) > 0 ? $exploded[0] : null;
	}
	
	
	public function getShopifyShopIdAttribute()
	{
		if(preg_match('/gid/', $this->shop_id)) {
			return sprintf('gid://shopify/Shop/%d', $this->shop_id);
		}
		else {
			return $this->shop_id;
		}
	}
	
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