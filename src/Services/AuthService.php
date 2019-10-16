<?php

namespace onefasteuro\ShopifyApps\Services;

use onefasteuro\ShopifyApps\Events\TokenWasReceived;
use onefasteuro\ShopifyClient\GraphClientInterface;
use onefasteuro\ShopifyClient\GraphResponse;
use onefasteuro\ShopifyUtils\ShopifyUtils;
use onefasteuro\ShopifyApps\Nonce;
use Illuminate\Contracts\Events\Dispatcher as EventsDispatcher;

class AuthService extends AbstractBaseService implements ServiceInterface
{
	const OAUTH_URL = 'https://%s/admin/oauth/authorize?client_id=%s&scope=%s&state=%s&redirect_uri=%s';
	const TOKEN_URL = 'https://%s/admin/oauth/access_token';
	
    protected $nonce;

	public function __construct(Nonce $nonce, EventsDispatcher $events)
    {
	    $this->nonce = $nonce;
    	parent::__construct($events);
    }
    
    /**
     * This is the URL that we redirect to for shopify to create the oauth token
     */
    public function getOAuthUrl($shop_domain)
    {
    	$domain = ShopifyUtils::formatDomain($shop_domain);
    	
        $redirect_url = $this->config('redirect_url');
        
        if($redirect_url === 'shopify.auth.complete') {
        	$redirect_url = route($redirect_url, ['app_id' => $this->config('app_id')]);
        }
        
        
        $nonce = $this->nonce->retrieve();
        $client_id = $this->config('client_id');
        $scope = $this->config('scope');
        
        $url = sprintf(static::OAUTH_URL, $domain, $client_id, $scope, $nonce, $redirect_url);
        
        return $url;
        
    }
	
	/**
	 * Get the needed info from our shop
	 * @param GraphClientInterface $client
	 * @return mixed
	 */
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
	    
	    $response = $client->query($call, []);
	    
	    if($response->isOk()) {
	    	$this->events->dispatch(new TokenWasReceived($response->body('access_token')));
	    }
	    
	    return $response;
    }
	
	/**
	 * Exchange an auth code for an application token
	 * @param $domain
	 * @param $code
	 * @return GraphResponse
	 */
    public function exchangeCodeForToken($domain, $code)
    {
    	$domain = ShopifyUtils::formatDomain($domain);
    	
	    $payload = [
		    'client_id' => $this->config('client_id'),
		    'client_secret' => $this->config('client_secret'),
		    'code' => $code
	    ];
	    
	    $url = sprintf(static::TOKEN_URL, $domain);
	    
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
