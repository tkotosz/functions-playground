<?php

namespace Tkotosz\Pipeline\Error;

use Error;

abstract class ErrorWithNamedConstructor extends Error
{
    use ThrowableWithNamedConstructor;
}
