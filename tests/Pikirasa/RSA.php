<?php
use Pikirasa\RSA;

class RSATest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->publicKeyPath  =  dirname(__DIR__) . '/fixtures/public2048.pem';
        $this->privateKeyPath =  dirname(__DIR__) . '/fixtures/private2048.pem';
        $this->publicKeyFile  = 'file://' . $this->publicKeyPath;
        $this->privateKeyFile = 'file://' . $this->privateKeyPath;

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

    public function testEncryptDecryptWithPath()
    {
        $rsa = new RSA($this->publicKeyPath, $this->privateKeyPath);
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

    public function testEncryptWithoutPrivateKey()
    {
        $rsa = new RSA($this->publicKeyFile);
        $data = 'abc123';
        $encrypted = $rsa->encrypt($data);
        $this->assertNotSame($encrypted, $data);
    }

    /**
     * @expectedException Pikirasa\Exception
     */
    public function testDecryptRequiresPrivateCertFile()
    {
        $rsa = new RSA($this->publicKeyFile);
        $data = 'abc123';
        $encrypted = $rsa->encrypt($data);
        $decrypted = $rsa->decrypt($encrypted);
    }

    public function testBase64EncryptDecrypt()
    {
        $rsa = new RSA($this->publicKeyFile, $this->privateKeyFile);
        $data = 'abc123';
        $encrypted = $rsa->base64Encrypt($data);

        // Ensure there are no unicode characters
        $this->assertRegExp('/[\x00-\x7F]/', $encrypted);

        $decrypted = $rsa->base64Decrypt($encrypted);
        $this->assertSame($decrypted, $data);
    }

    public function testCreateAndEncryptWithFile()
    {
        $tempPublicKeyFile = 'file://' . tempnam(sys_get_temp_dir(), get_class($this));
        $tempPrivateKeyFile = 'file://' . tempnam(sys_get_temp_dir(), get_class($this));
        $password = 'foobar';

        $rsa = new RSA($tempPublicKeyFile, $tempPrivateKeyFile, $password);
        $success = $rsa->create(null, true);
        $this->assertTrue($success);

        $data = 'abc123';
        $encrypted = $rsa->encrypt($data);
        $decrypted = $rsa->decrypt($encrypted);
        $this->assertSame($decrypted, $data);

        unlink($tempPublicKeyFile);
        unlink($tempPrivateKeyFile);
    }

    public function testCreateAndEncryptWithString()
    {
        $password = 'foobar';

        $rsa = new RSA(null, null, $password);
        $success = $rsa->create();
        $this->assertTrue($success);

        $data = 'abc123';
        $encrypted = $rsa->encrypt($data);
        $decrypted = $rsa->decrypt($encrypted);
        $this->assertSame($decrypted, $data);
    }

    public function testCreateAndEncryptWithKeysize()
    {
        $password = 'foobar';

        $rsa = new RSA(null, null, $password);
        $success = $rsa->create(4096);
        $this->assertTrue($success);

        $data = 'abc123';
        $encrypted = $rsa->encrypt($data);
        $decrypted = $rsa->decrypt($encrypted);
        $this->assertSame($decrypted, $data);
    }

    public function testCreateAndRetrieveWithString()
    {
        $password = 'foobar';

        $rsa = new RSA(null, null, $password);
        $success = $rsa->create();
        $this->assertTrue($success);

        $this->assertNotEmpty($rsa->getPublicKeyFile());
        $this->assertNotEmpty($rsa->getPrivateKeyFile());
        $this->assertNotSame($rsa->getPublicKeyFile(), $rsa->getPrivateKeyFile());
    }
}
