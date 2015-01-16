<?php
// The namespace is important, it allows us to override openssl_error_string in
// that particular namespace. Note that namespaces are otherwise pretty useless
// in a phpunit test. I would have written namespace Pikirasa\Tests to help
// locating the file otherwise, but that is pretty much the only use.
//
// @see http://www.schmengler-se.de/en/2011/03/php-mocking-built-in-functions-like-time-in-unit-tests/
namespace Pikirasa;

use Pikirasa\Exception;

function openssl_error_string()
{
    if (ExceptionTest::$complain) {
        return 'This is an openssl error message';
    }

    return false;
}

class ExceptionTest extends \PHPUnit_Framework_TestCase
{
    public static $complain;

    public function testToStringContainsLibraryAndOpenSSLErrorMessages()
    {
        self::$complain = true;
        $exception = new Exception('This is a pikirasa message');
        $this->assertContains('This is an openssl error message', $exception->getMessage());
        $this->assertContains('This is a pikirasa message', $exception->getMessage());
    }

    public function testErrorMessageIsNormalWhenOpenSSLStaysQuiet()
    {
        self::$complain = false;
        $exception = new Exception('This is a pikirasa message');
        $this->assertSame('This is a pikirasa message', $exception->getMessage());
    }
}
