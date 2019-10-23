<?php

namespace Apidae\ApiClient\Exception;

class MissingTokenException extends \InvalidArgumentException
{
    protected $message = <<<MESSAGE
Please provide an access token for the scope you are requesting.
MESSAGE;
}
