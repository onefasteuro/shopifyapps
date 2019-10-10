<?php

namespace onefasteuro\ShopifyApps;




use onefasteuro\ShopifyApps\Models\ShopifyApp;

class Helpers
{
	
	const NS = 'shopifyapps';
	const URL_AUTHORIZE = 'https://%s.myshopify.com/admin/oauth/authorize?client_id=%s&scope=%s&state=%s&redirect_uri=%s';
	const URL_FOR_TOKEN = 'https://%s/admin/oauth/access_token';
	
	private $nonce;
	

	
	
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
	
	public function getOauthUrl($domain)
	{
		return sprintf(static::URL_FOR_TOKEN, $domain);
	}
	
	
	public function getReturnUrl(ShopifyApp $app)
	{
        $url = $this->getAppConfig('return_url');

        switch($url)
        {
            case 'route':
            break;

            default:
                return $app->app_launch_url;
                break;
        }
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
