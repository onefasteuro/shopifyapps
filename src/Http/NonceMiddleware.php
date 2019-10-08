<?php

namespace onefasteuro\ShopifyAuth\Http;


use Closure;
use onefasteuro\ShopifyAuth\Nonce;

class NonceMiddleware
{
	
	protected $nonce;
	
	public function __construct(Nonce $n)
	{
		$this->nonce = $n;
	}
	
	public function handle($request, Closure $next)
	{
		$this->setNonceStore($request);
		
		return $next($request);
	}
	
	public function setNonceStore($request)
	{
		$this->nonce->setStore($request->session());
	}
}