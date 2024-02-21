<?php

namespace Webdevartisan\LaravelBlocker\Exceptions;

class BlockedUserException extends \Exception
{
    protected $code = 406;
}
