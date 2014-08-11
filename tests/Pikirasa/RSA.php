<?php
use Pikirasa\RSA;

class RSATest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->publicKeyFile = 'file://' . dirname(__DIR__) . '/fixtures/public2048.pem';
        $this->privateKeyFile = 'file://' . dirname(__DIR__) . '/fixtures/private2048.pem';

        $this->publicPasswordKeyFile = 'file://' . dirname(__DIR__) . '/fixtures/public2048-password.pem';
        $this->privatePasswordKeyFile = 'file://' . dirname(__DIR__) . '/fixtures/private2048-password.pem';
    }

    public function testEncryptDecryptWithFile()
    {
        $rsa = new RSA($this->publicKeyFile, $this->privateKeyFile);
        $data = 'abc123';
        $encrypted = $rsa->encrypt($data);
        $decrypted = $rsa->decrypt($encrypted);
        $this->assertSame($decrypted, $data);
    }

    public function testEncryptDecryptWithString()
    {
        $rsa = new RSA(file_get_contents($this->publicKeyFile), file_get_contents($this->privateKeyFile));
        $data = 'abc123';
        $encrypted = $rsa->encrypt($data);
        $decrypted = $rsa->decrypt($encrypted);
        $this->assertSame($decrypted, $data);
    }

    public function testEncryptDecryptWithPassword()
    {
        $rsa = new RSA($this->publicPasswordKeyFile, $this->privatePasswordKeyFile, 'foobar');
        $data = 'abc123 please encrypt me';
        $encrypted = $rsa->encrypt($data);
        $decrypted = $rsa->decrypt($encrypted);
        $this->assertSame($decrypted, $data);
    }

    /**
     * @expectedException Pikirasa\Exception
     */
    public function testEncryptDecryptWithWrongPassword()
    {
        $rsa = new RSA($this->publicPasswordKeyFile, $this->privatePasswordKeyFile, 'incorrect_password');
        $data = 'abc123 please encrypt me';
        $encrypted = $rsa->encrypt($data);
        $decrypted = $rsa->decrypt($encrypted);
        $this->assertSame($decrypted, $data);
    }

    /**
     * @expectedException Pikirasa\Exception
     */
    public function testEncryptExceptionWithInvalidCertFile()
    {
        $rsa = new RSA($this->publicKeyFile . 'blahblah', $this->privateKeyFile);
        $data = 'abc123';
        $encrypted = $rsa->encrypt($data);
    }

    /**
     * @expectedException Pikirasa\Exception
     */
    public function testDecryptExceptionWithInvalidCertFile()
    {
        $rsa = new RSA($this->publicKeyFile, $this->privateKeyFile . 'blahblah');
        $data = 'abc123';
        $encrypted = $rsa->encrypt($data);
        $decrypted = $rsa->decrypt($encrypted);
    }
}
