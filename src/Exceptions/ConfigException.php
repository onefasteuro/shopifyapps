<?php

namespace onefasteuro\ShopifyApps\Exceptions;



class ConfigException extends \Exception
{
	protected static $message_format = 'The %s key is missing from the config';
	
	
	public static function factory($key)
	{
		$message = sprintf(static::$message_format, $key);
		return new static($message, 100);
	}
	
}
