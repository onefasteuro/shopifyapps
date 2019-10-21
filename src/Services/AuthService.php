<?php

namespace onefasteuro\ShopifyApps\Services;


use onefasteuro\ShopifyClient\GraphResponse;
use onefasteuro\ShopifyUtils\ShopifyUtils;
use Illuminate\Support\Facades\URL;
use Requests as HttpClient;

class AuthService
{
	const OAUTH_URL = 'https://%s/admin/oauth/authorize?client_id=%s&scope=%s&state=%s&redirect_uri=%s';
	const TOKEN_URL = 'https://%s/admin/oauth/access_token';


    public static function assertNonce($request_state, $nonce)
    {
        //nonce do not match, error out
        return $nonce === $request_state;
    }

    public static function assertDomain($domain)
    {
        return preg_match('/[a-zA-Z0-9\-]+\.myshopify\.com/', $domain);
    }


    public static function assertHMAC($query, $secret)
    {
        $hmac = $query['hmac'];
        if(array_key_exists('hmac', $query) ) {
            unset($query['hmac']);
        }

        if(array_key_exists('signature', $query)) {
            unset($query['signature']);
        }

        //sort the array by key
        ksort($query);


        $data = urldecode(http_build_query($query));


        $calc = hash_hmac('sha256', $data, $secret);

        return $calc == $hmac;
    }


    public static function getOAuthUrl($shop_domain, $client_id, $scope, $state, $redirect_url)
    {
    	$domain = ShopifyUtils::formatDomain($shop_domain);
        
        $url = sprintf(static::OAUTH_URL, $domain, $client_id, $scope, $state, URL::to($redirect_url));
        
        return $url;
        
    }
    
    
    public static function exchangeCodeForToken($domain, $code, $client_id, $client_secret)
    {
    	$domain = ShopifyUtils::formatDomain($domain);
    	
	    $payload = [
		    'client_id' => $client_id,
		    'client_secret' => $client_secret,
		    'code' => $code
	    ];
	    
	    $url = sprintf(static::TOKEN_URL, $domain);
	    
	    $response = HttpClient::post($url, [], $payload);
	    return new GraphResponse($response);
    }

}
