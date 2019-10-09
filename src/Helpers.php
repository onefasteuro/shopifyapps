<?php

namespace onefasteuro\ShopifyAuth;




class Helpers
{
	private $config = [];
	private $appname = null;
	private $nonce;
	
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
		$base_url = $this->config['shop_auth_url'];
		
		//redirect url
		$route = $this->config['redirect_url'];
		$redirect = route($route, ['appname' => $this->appname]);
		
		$client_id = $this->getClientId();
		
		$scope = $this->getScope();
		
		$state = $this->nonce->createAndSave();
		
		//get the URL
		$url = sprintf($base_url, $shop, $client_id, $scope, $state, $redirect);
		
		return $url;
	}
	
	public function getOauthUrl($domain)
	{
		return sprintf($this->config['oauth_url'], $domain);
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
	
	public function getReturnUrl(array $query_vars = [])
	{
		$domain = $query_vars['shop_domain'];
		$r = $this->getAppConfig('return_url');
		
		if(preg_match('/\.com/', $r)) {
			return $r . '?' . http_build_query($query_vars);
		}
		else {
			$url = sprintf($r, $domain);
			return $url . '?' . http_build_query($query_vars);
		}
	}
	
	protected function getAppConfig($key)
	{
		if($this->appname === null) {
			throw new \Exception('Appname is not defined on ' . __CLASS__);
		}
		
		return $this->config['apps'][$this->appname][$key];
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
