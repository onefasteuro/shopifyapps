<?php

namespace onefasteuro\ShopifyApps\Exceptions;



class MissingChargeFormatException extends \Exception
{
	protected static $message_format = 'There is no format of type: %s';
	
	public function factory($type)
	{
		$message = sprintf(static::$message_format, $type);
		return new static($message, 100);
	}
}

