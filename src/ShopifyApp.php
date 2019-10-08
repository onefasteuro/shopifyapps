<?php

namespace onefasteuro\ShopifyAuth;


use Illuminate\Database\Eloquent\Model;

class ShopifyApp extends Model
{
	protected $table = 'shopify_apps';
	
	protected $fillable = [
		'shop_domain',
		'app_name',
		'shop_id',
		'shop_name'
	];
	
	//appends to the toArray output
	protected $appends = ['shopify_shop_id'];
	
	public function bill()
	{
		return $this->hasOne(ShopifyBilling::class, 'app_id','id')->withDefault();
	}
	
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
	
	public static function findWithBilling($id)
	{
		return static::with('bill')->find($id);
	}
}