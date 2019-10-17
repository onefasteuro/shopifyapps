<?php

namespace onefasteuro\ShopifyApps\Models;


use Illuminate\Database\Eloquent\Model;
use onefasteuro\ShopifyUtils\ShopifyUtils;

class ShopifyApp extends Model
{
	protected $table = 'shopify_apps';
	
	
	public function bills()
	{
		return $this->hasMany(ShopifyBilling::class, 'app_id','id')->withDefault();
	}
	
	public function setAppInstallationIdAttribute($value)
	{
		$this->attributes['app_installation_id'] = ShopifyUtils::gidParse($value);
	}
	
	public function getMyshopifyDomainAttribute()
	{
		return sprintf('https://%s.myshopify.com', $this->shop_handle);
	}
	
	public function getShopGidAttribute()
	{
		return ShopifyUtils::gidRestore($this->shop_id, 'Shop');
	}
}

