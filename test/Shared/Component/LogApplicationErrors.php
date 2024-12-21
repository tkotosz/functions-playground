<?php

namespace Tkotosz\Pipeline\Test\Shared\Component;

use Error;
use Tkotosz\Pipeline\Error\RejectPipelineInput;

class LogApplicationErrors
{
    public function __construct() {}

    public static function create(): self
    {
        return new self();
    }

    public function __invoke(Error $error): Error
    {
        file_put_contents(
            'var/log/system.log',
            sprintf('[APP ERROR] %s', (string)$error) . PHP_EOL,
            FILE_APPEND
        );

        return $error;
    }
}
