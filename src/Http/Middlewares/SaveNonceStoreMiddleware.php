<?php


namespace onefasteuro\ShopifyApps\Http\Middlewares;

class SaveNonceStoreMiddleware extends SetNonceStoreMiddleware
{
	public function handle($request, \Closure $next)
	{
        $this->nonce->createAndSave();
		return $next($request);
	}
}