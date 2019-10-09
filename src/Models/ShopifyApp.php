<?php

namespace onefasteuro\ShopifyAuth\Models;


use Illuminate\Database\Eloquent\Model as BaseModel;
use onefasteuro\ShopifyAuth\Helpers;

class ShopifyApp extends BaseModel
{
	protected $table = 'shopify_apps';
	
	//appends to the toArray output
	protected $appends = ['shop_gid', 'app_installation_gid'];
	
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
	
	public function setShopIdAttribute($value)
	{
		$this->attributes['shop_id'] = Helpers::gidParse($value);
	}
	
	
	public function setAppInstallationIdAttribute($value)
	{
		$this->attributes['app_installation_id'] = Helpers::gidParse($value);
	}
	
	public function getShopGidAttribute()
	{
		return Helpers::gidRestore($this->shop_id, 'Shop');
	}
	
	public function getAppInstallationGidAttribute()
	{
		return Helpers::gidRestore($this->app_installation_id, 'AppInstallation');
	}
}

