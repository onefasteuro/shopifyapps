<?php

namespace onefasteuro\ShopifyAuth\Http;


class NonceMiddleware
{
	
	protected $nonce;
	protected $helpers;
	
	public function __construct(\onefasteuro\ShopifyAuth\Nonce $n, \onefasteuro\ShopifyAuth\Helpers $h)
	{
		$this->helpers = $h;
		$this->nonce = $n;
	}
	
	public function handle($request, \Closure $next)
	{
		$this->setNonceStore($request);
		$this->setHelpersAppName($request);
		
		return $next($request);
	}
	
	public function setHelpersAppName($request)
	{
		$this->helpers->setAppName($request->route()->parameter('appname'));
	}
	
	public function setNonceStore($request)
	{
		$this->nonce->setStore($request->session());
	}
}