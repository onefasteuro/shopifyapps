<?php

namespace onefasteuro\ShopifyApps\Auth;

use Illuminate\Support\Arr;
use onefasteuro\ShopifyApps\Nonce;
use onefasteuro\ShopifyUtils\ShopifyUtils;

class ShopifyVerifyOAuthRequest
{

    public function __construct(array $config)
    {
    }

    public function verify()
    {
        $this->assertDomain();
        $this->assertHMAC();
        $this->assertNonce();
    }

    public function assertNonce(Nonce $nonce, $request_nonce)
    {
        $nonce = $nonce->retrieve();

        //nonce do not match, error out
        return $nonce === $request_nonce;
    }


    /**
     * Assert the Shop Domain
     * @param $shop_domain
     * @return bool
     */
    public function assertDomain($shop_domain)
    {
        return preg_match('/[a-zA-Z0-9\-]+\.myshopify\.com/', $shop_domain) ? true : false;
    }


    public function assertHMAC(array $query)
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

        //$secret = client secret

        $calc = hash_hmac('sha256', $data, $secret);

        return $calc == $hmac;
    }

}
