<?php

namespace onefasteuro\ShopifyApps\Auth;

use Illuminate\Support\Arr;
use onefasteuro\ShopifyApps\Nonce;
use Illuminate\Http\Request;

class ShopifyVerifyOAuthRequest
{
    protected $config = [];
    protected $nonce;

    public function __construct(array $config, Nonce $nonce)
    {
        $this->config = $config;
        $this->nonce = $nonce;
    }

    public function verify(Request $request)
    {
        $this->assertDomain($request);
        $this->assertHMAC($request);
        $this->assertNonce($request);
    }

    protected function assertNonce(\Illuminate\Http\Request $request)
    {
        $nonce = $this->nonce->retrieve();

        //nonce do not match, error out
        return $nonce === $request->query('state');
    }

    protected function assertDomain(\Illuminate\Http\Request $request)
    {
        $domain = $request->get('shop');

        return preg_match('/[a-zA-Z0-9\-]+\.myshopify\.com/', $domain);
    }


    protected function assertHMAC(\Illuminate\Http\Request $request)
    {
        $query = $request->query();

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


        $secret = Arr::get($this->config, 'appname.client_secret');

        $calc = hash_hmac('sha256', $data, $secret);

        return $calc == $hmac;
    }

}
