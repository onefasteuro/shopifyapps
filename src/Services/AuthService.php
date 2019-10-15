<?php

namespace onefasteuro\ShopifyApps\Services;

use onefasteuro\ShopifyClient\GraphClientInterface;
use onefasteuro\ShopifyClient\GraphResponse;
use onefasteuro\ShopifyUtils\ShopifyUtils;
use onefasteuro\ShopifyApps\Nonce;
use Illuminate\Contracts\Events\Dispatcher as EventsDispatcher;

class AuthService extends BaseService
{
	const OAUTH_URL = 'https://%s/admin/oauth/authorize?client_id=%s&scope=%s&state=%s&redirect_uri=%s';
	const TOKEN_URL = 'https://%s/admin/oauth/access_token';
	
    protected $nonce;

	public function __construct(Nonce $nonce, EventsDispatcher $events)
    {
    	parent::__construct($events);
        $this->nonce = $nonce;
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
    
    public function getShopInfo(GraphClientInterface $client)
    {
	    $call = 'query {
			  app: appInstallation {
			    id
			    launchUrl
			    uninstallUrl
			    current: app {
			        id
			        handle
			    }
			  }
			  shop {
			    id
			    name
			    email
			    domain: myshopifyDomain
			  }
			}';
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
