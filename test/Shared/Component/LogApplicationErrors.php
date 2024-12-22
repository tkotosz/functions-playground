<?php

namespace Tkotosz\Pipeline\Test\Shared\Component;

use Error;
use Exception;
use Throwable;

class LogApplicationErrors
{
    public function __construct() {}

    public static function create(): self
    {
        return new self();
    }

    public function __invoke(Error $error): Error
    {
        try {
            $this->log($error);
        } catch (Throwable $e) {
            // IO Error - Could not log - return error, but also preseve original error
            $error = new Error('Error logging failed', previous: $error);
        }

        return $error;
    }

    private function log(Error $error): void
    {
        if (!is_writable('var/log')) {
            throw new Exception('Cannot write log!');
        }

        file_put_contents(
            'var/log/system.log',
            sprintf('[APP ERROR] %s', (string)$error) . PHP_EOL,
            FILE_APPEND
        );
    }
}
