<?php

namespace onefasteuro\ShopifyApps\Models;


use Illuminate\Database\Eloquent\Model as BaseModel;
use onefasteuro\ShopifyApps\Helpers;

class ShopifyApp extends BaseModel implements \onefasteuro\ShopifyApps\Contracts\ModelContract
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
	public static function findInstallation(string $gid)
	{
	    $id = Helpers::gidParse($gid);

		return static::where('app_installation_id', '=', $id)->first();
	}
	
	public function bill()
	{
		return $this->hasOne(ShopifyBilling::class, 'app_id','id')->withDefault();
	}
	
	public static function findWithBilling($id)
	{
		return static::with('bill')->find($id);
	}
	
	public function setShopIdAttribute($value)
	{
		$this->attributes['shop_id'] = Helpers::gidParse($value);
	}
	
	
	public function setAppInstallationIdAttribute($value)
	{
		$this->attributes['app_installation_id'] = Helpers::gidParse($value);
	}
	
	public function getFqdnAttribute()
	{
		if(!preg_match('/http\:\/\//', $this->shop_domain)) {
			return sprintf('https://%s', $this->shop_domain);
		}
		
		return $this->shop_domain;
	}
	
	public function getShopGidAttribute()
	{
		return Helpers::gidRestore($this->shop_id, 'Shop');
	}
	
	public function getAppInstallationGidAttribute()
	{
		return Helpers::gidRestore($this->app_installation_id, 'AppInstallation');
	}
	
	public function getLaunchUrlAttribute()
	{
		return $this->app_launch_url;
	}
	
	
	public function getReturnUrlAttribute()
	{
		$url = Helpers::config($this->app_name, 'return_url');
		switch($url) {
			
			default:
				return $this->launch_url;
				break;
		}
	}
}

