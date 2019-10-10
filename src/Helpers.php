<?php

namespace onefasteuro\ShopifyApps;




use onefasteuro\ShopifyApps\Models\ShopifyApp;

class Helpers
{
	private $config = [];
	private $appname = null;
	private $nonce;
	
	const URL_AUTHORIZE = 'https://%s.myshopify.com/admin/oauth/authorize?client_id=%s&scope=%s&state=%s&redirect_uri=%s';
	const URL_FOR_TOKEN = 'https://%s/admin/oauth/access_token';

	protected $model;

	public function __construct(array $config, Nonce $n)
	{
		$this->config = $config;
		$this->nonce = $n;
	}
	
	public function setAppName($a)
	{
		$this->appname = $a;
		return $this;
	}
	
	public function appName()
	{
		return $this->appname;
	}
	
	
	public function getShopAuthUrl($shop)
	{
		//redirect url
		$redirect = route('shopifyauth.handle', ['appname' => $this->appname]);
		
		$client_id = $this->getClientId();
		
		$scope = $this->getScope();
		
		$state = $this->nonce->createAndSave();
		
		//get the URL
		$url = sprintf(static::URL_AUTHORIZE, $shop, $client_id, $scope, $state, $redirect);
		
		return $url;
	}
	
	public function getOauthUrl($domain)
	{
		return sprintf(static::URL_FOR_TOKEN, $domain);
	}
	
	
	public function getScope()
	{
		return $this->getAppConfig('scope');
	}
	
	public function getClientId()
	{
		return $this->getAppConfig('client_id');
	}
	
	public function getClientSecret()
	{
		return $this->getAppConfig('client_secret');
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
	
	protected function getAppConfig($key)
	{
		if($this->appname === null) {
			throw new \Exception('Appname is not defined on ' . __CLASS__);
		}
		
		return $this->config[$this->appname][$key];
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
