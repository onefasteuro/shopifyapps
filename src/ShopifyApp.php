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
	
	public function setAppInstallationIdAttribute($value)
	{
		$value = Helpers::gidParse($value);
		$this->attributes['app_installation_id'] = $value;
	}
	
	public function getAppInstallationGidAttribute()
	{
		return 'gid://shopify/AppInstallation/' . $this->app_installation_id;
	}
}