<?php

namespace Tkotosz\Pipeline\Error;

use Error;
use ReflectionProperty;
use Throwable;

abstract class ErrorWithNamedConstructor extends Error
{
    protected function __construct(string $message = "", int $code = 0, Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
		
		// muhahaha
		$traceProp = new ReflectionProperty(Error::class, 'trace');
		$traceProp->setAccessible(true);
		$trace = $traceProp->getValue($this);
		
		$lineProp = new ReflectionProperty(Error::class, 'line');
		$lineProp->setAccessible(true);
		$lineProp->setValue($this, $trace[0]['line']);
		
		$fileProp = new ReflectionProperty(Error::class, 'file');
		$fileProp->setAccessible(true);
		$fileProp->setValue($this, $trace[0]['file']);
		
		array_shift($trace);
		$traceProp->setValue($this, $trace);
	}
}
