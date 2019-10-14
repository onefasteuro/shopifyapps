<?php

namespace onefasteuro\ShopifyApps\Auth;

use Illuminate\Support\Arr;
use onefasteuro\ShopifyApps\Exceptions\ConfigException;
use onefasteuro\ShopifyApps\Repositories\GraphqlRepository;
use onefasteuro\ShopifyClient\GraphClient;
use onefasteuro\ShopifyClient\GraphResponse;
use onefasteuro\ShopifyUtils\ShopifyUtils;
use onefasteuro\ShopifyApps\Nonce;
use Illuminate\Contracts\Events\Dispatcher as EventsDispatcher;
use Illuminate\Contracts\Config\Repository as ConfigRepository;

class ShopifyAuthService
{
	const OAUTH_URL = 'https://%s/admin/oauth/authorize?client_id=%s&scope=%s&state=%s&redirect_uri=%s';
	const TOKEN_URL = 'https://%s/admin/oauth/access_token';
	
	
    protected $nonce;
    protected $events;
    protected $config = [];

    //the active app that we draw config etc from
    protected $shopify_app = null;
    protected $shopify_domain;

	public function __construct(Nonce $nonce, EventsDispatcher $events)
    {
        $this->nonce = $nonce;
        $this->events = $events;
    }
    
    public function setShopifyAppConfig(array $config)
    {
    	$config = $this->validateConfig($config);
    	$this->config = $config;
    	return $this;
    }
    
    protected function validateConfig(array $config)
    {
    	//params we need to check
    	$params = [
    		'client_id',
		    'client_secret',
		    'return_url',
		    'redirect_url',
		    'scope'
	    ];
    	
    	foreach($params as $key)
	    {
		    if(!array_key_exists($key, $config)) {
			    throw new ConfigException('The '.$key.' key is missing from the config');
		    }
	    }
	    
    	return $config;
    }

    public function setShopifyApp($app)
    {
        $this->shopify_app = $app;
        return $this;
    }

    public function setShopifyDomain($domain)
    {
        $this->shopify_domain = ShopifyUtils::formatDomain($domain);
        return $this;
    }


    /**
     * This is the URL that we redirect to for shopify to create the oauth token
     */
    public function getOAuthUrl()
    {
        $redirect_url = $this->config['redirect_url'];
        
        if($redirect_url === 'shopify.auth.complete') {
        	$redirect_url = route($redirect_url, ['shopify_app_name' => $this->shopify_app]);
        }
        
        
        $nonce = $this->nonce->retrieve();
        $client_id = $this->config['client_id'];
        $scope = $this->config['scope'];
        
        $url = sprintf(static::OAUTH_URL, $this->shopify_domain, $client_id, $scope, $nonce, $redirect_url);
        
        return $url;
        
    }
    
    
    public function exchangeCodeForToken($code)
    {
	    $payload = [
		    'client_id' => $this->config['client_id'],
		    'client_secret' => $this->config['client_secret'],
		    'code' => $code
	    ];
	    
	    $url = sprintf(static::TOKEN_URL, $this->shopify_domain);
	    
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
	    curl_setopt($ch, CURLOPT_POST, true);
	    curl_setopt($ch, CURLOPT_HEADER, true);
	    curl_setopt($ch, CURLOPT_VERBOSE, false);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
	    
	    $response = curl_exec($ch);
	    
	    //parse the response
	    $token_response = ShopifyUtils::parseBody($response);
	    $token_code = ShopifyUtils::parseStatusCode($response);
	    $token_headers = ShopifyUtils::parseHeaders($response);
	    
	    curl_close($ch);
	    
	    return new GraphResponse($token_headers, $token_code, $token_response);
    }

}
