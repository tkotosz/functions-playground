<?php

namespace Tkotosz\Pipeline\Error;

use ReflectionProperty;
use Throwable;

trait ThrowableWithNamedConstructor
{
    protected function __construct(string $message = "", int $code = 0, Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
		
		$traceProp = new ReflectionProperty(parent::class, 'trace');
		$traceProp->setAccessible(true);
		$trace = $traceProp->getValue($this);
		
		$lineProp = new ReflectionProperty(parent::class, 'line');
		$lineProp->setAccessible(true);
		$lineProp->setValue($this, $trace[0]['line']);
		
		$fileProp = new ReflectionProperty(parent::class, 'file');
		$fileProp->setAccessible(true);
		$fileProp->setValue($this, $trace[0]['file']);
		
		array_shift($trace);
		$traceProp->setValue($this, $trace);
	}
}
