<?php

namespace onefasteuro\ShopifyApps;


class Helpers
{
	
	const NS = 'shopifyapps';
	const URL_AUTHORIZE = 'https://%s.myshopify.com/admin/oauth/authorize?client_id=%s&scope=%s&state=%s&redirect_uri=%s';
	const URL_FOR_TOKEN = 'https://%s/admin/oauth/access_token';
	
	
	public static function config($appname, $key)
	{
		return config(static::NS . '.' . $appname . '.' . $key);
	}
	
	
	public static function getShopAuthUrl($appname, $shop, $nonce)
	{
		//redirect url
		$redirect = route('shopify.auth.handle', ['appname' => $appname]);
		
		$client_id = static::config($appname, 'client_id');
		
		$scope = static::config($appname, 'scope');
		
		//get the URL
		$url = sprintf(static::URL_AUTHORIZE, $shop, $client_id, $scope, $nonce, $redirect);
		
		return $url;
	}
	
	public static function getBillingProvider($appname)
	{
		$plans_class = Helpers::config($appname, 'billing');
		return $plans_class;
	}
	
	/**
	 * Parses a GID and returns an id
	 * @param $gid
	 * @return int
	 */
	public static function gidParse($gid)
	{
		return intval(preg_replace('/[^0-9]/', '', $gid));
	}
	
	public static function gidRestore($id, $namespace)
	{
		$id = (int) $id;
		return sprintf('gid://shopify/%s/%d', $namespace, $id);
	}
}
