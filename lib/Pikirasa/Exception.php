<?php
namespace Pikirasa;

class Exception extends \Exception
{
    public function __construct($message, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        if ($openSSlErrorMessage = openssl_error_string()) {
            // openSSL has something to say! Let us add it to the message.
            $this->message = sprintf(
                '%s%sUnderlying openSSL message : %s',
                parent::getMessage(),
                PHP_EOL,
                $openSSlErrorMessage
            );
        }
    }
}
