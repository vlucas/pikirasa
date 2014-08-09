<?php
use Pikirasa\RSA;

class RSATest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->rsa = new RSA();
        $this->publicKeyFile = 'file://' . dirname(__DIR__) . '/fixtures/public2048.pem';
        $this->privateKeyFile = 'file://' . dirname(__DIR__) . '/fixtures/private2048.pem';
    }

    public function testEncryptDecryptWithFile()
    {
        $data = 'abc123';
        $encrypted = $this->rsa->encrypt($this->publicKeyFile, $data);
        $decrypted = $this->rsa->decrypt($this->privateKeyFile, $encrypted);
        $this->assertSame($decrypted, $data);
    }

    public function testEncryptDecryptWithString()
    {
        $data = 'abc123';
        $encrypted = $this->rsa->encrypt(file_get_contents($this->publicKeyFile), $data);
        $decrypted = $this->rsa->decrypt(file_get_contents($this->privateKeyFile), $encrypted);
        $this->assertSame($decrypted, $data);
    }

    /**
     * @expectedException Pikirasa\Exception
     */
    public function testEncryptExceptionWithInvalidCertFile()
    {
        $data = 'abc123';
        $encrypted = $this->rsa->encrypt($this->publicKeyFile . 'blahblah', $data);
    }

    /**
     * @expectedException Pikirasa\Exception
     */
    public function testDecryptExceptionWithInvalidCertFile()
    {
        $data = 'abc123';
        $encrypted = $this->rsa->encrypt($this->publicKeyFile, $data);
        $decrypted = $this->rsa->decrypt($this->privateKeyFile . 'blahblah', $encrypted);
    }
}

