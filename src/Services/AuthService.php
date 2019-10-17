<?php

namespace onefasteuro\ShopifyApps\Services;


use onefasteuro\ShopifyClient\GraphResponse;
use onefasteuro\ShopifyUtils\ShopifyUtils;
use Illuminate\Support\Facades\URL;

class AuthService implements AuthServiceInterface
{
	const OAUTH_URL = 'https://%s/admin/oauth/authorize?client_id=%s&scope=%s&state=%s&redirect_uri=%s';
	const TOKEN_URL = 'https://%s/admin/oauth/access_token';
	

    public function getOAuthUrl($shop_domain, $client_id, $scope, $state, $redirect_url)
    {
    	$domain = ShopifyUtils::formatDomain($shop_domain);
        
        $url = sprintf(static::OAUTH_URL, $domain, $client_id, $scope, $state, URL::to($redirect_url));
        
        return $url;
        
    }
    
    
    public function exchangeCodeForToken($domain, $code, $client_id, $client_secret)
    {
    	$domain = ShopifyUtils::formatDomain($domain);
    	
	    $payload = [
		    'client_id' => $client_id,
		    'client_secret' => $client_secret,
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
