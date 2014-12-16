<?php
namespace Pikirasa;

class RSA
{
    /*
     * Minimum key size bits
     */
    const MINIMUM_KEY_SIZE = 128;

    /*
     * Default key size bits
     */
    const DEFAULT_KEY_SIZE = 2048;

    protected $publicKeyFile;
    protected $privateKeyFile;
    protected $password;

    public function __construct($publicKeyFile, $privateKeyFile = null, $password = null)
    {
        $this->publicKeyFile =  $this->fixKeyArgument($publicKeyFile);
        $this->privateKeyFile = $this->fixKeyArgument($privateKeyFile);
        $this->password = $password;
    }

    public function fixKeyArgument($keyFile)
    {
        if (strpos($keyFile, '/') === 0) {
            // This looks like a path, let us prepend the file scheme
            return 'file://' . $keyFile;
        }

        return $keyFile;
    }


    /**
     * Creates a new RSA key pair with the given key size
     *
     * @param bool $overwrite Overwrite existing key files
     * @param null $keySize RSA Key Size in bits
     * @return bool Resule of creation
     *
     * @throws Pikirasa\Exception
     */
    public function create($overwrite = false, $keySize = null)
    {
        $keySize = intval($keySize);
        if (intval($keySize) < self::MINIMUM_KEY_SIZE) {
            $keySize = self::DEFAULT_KEY_SIZE;
        }

        if (!$overwrite) {
            if (file_exists($this->publicKeyFile) || file_exists($this->privateKeyFile)) {
                throw new Exception("OpenSSL: Existing keys found. Remove keys or use \$overwrite argument.");
            }
        }

        $config = array(
            'private_key_bits' => $keySize,
            'private_key_type' => OPENSSL_KEYTYPE_RSA
        );

        $resource = openssl_pkey_new($config);
        $pkey = openssl_pkey_get_details($resource);
        $pkeyResource = fopen($this->publicKeyFile, 'w+');
        $bytes = fwrite($pkeyResource, $pkey['key']);
        fclose($pkeyResource);

        if (strlen($pkey['key']) < 1 || $bytes != strlen($pkey['key'])) {
            throw new Exception("OpenSSL: Error writing PUBLIC key.");
        }

        $private = '';
        openssl_pkey_export($resource, $private, $this->password);
        $pkeyResource = fopen($this->privateKeyFile, 'w+');
        $bytes = fwrite($pkeyResource, $private);
        fclose($pkeyResource);

        if (strlen($private) < 1 || $bytes != strlen($private)) {
            throw new Exception("OpenSSL: Error writing PRIVATE key.");
        }

        return true;
    }

    /**
     * Set password to be used during encryption and decryption
     *
     * @param string $password Certificate password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Encrypt data with provided public certificate
     *
     * @param string $data Data to encrypt
     * @return string Encrypted data
     *
     * @throws Pikirasa\Exception
     */
    public function encrypt($data)
    {
        // Load public key
        $publicKey = openssl_pkey_get_public($this->publicKeyFile);

        if (!$publicKey) {
            throw new Exception("OpenSSL: Unable to get public key for encryption. Is the location correct? Does this key require a password?");
        }

        $success = openssl_public_encrypt($data, $encryptedData, $publicKey);
        openssl_free_key($publicKey);
        if (!$success) {
            throw new Exception("Encryption failed. Ensure you are using a PUBLIC key.");
        }

        return $encryptedData;
    }

    /**
     * Encrypt data and then base64_encode it
     *
     * @param string $data Data to encrypt
     * @return string Base64-encrypted data
     */
    public function base64Encrypt($data)
    {
        return base64_encode($this->encrypt($data));
    }

    /**
     * Decrypt data with provided private certificate
     *
     * @param string $data Data to encrypt
     * @return string Decrypted data
     *
     * @throws Pikirasa\Exception
     */
    public function decrypt($data)
    {
        if ($this->privateKeyFile === null) {
            throw new Exception("Unable to decrypt: No private key provided.");
        }

        $privateKey = openssl_pkey_get_private($this->privateKeyFile, $this->password);
        if (!$privateKey) {
            throw new Exception("OpenSSL: Unable to get private key for decryption");
        }

        $success = openssl_private_decrypt($data, $decryptedData, $privateKey);
        openssl_free_key($privateKey);
        if (!$success) {
            throw new Exception("Decryption failed. Ensure you are using (1) A PRIVATE key, and (2) the correct one.");
        }

        return $decryptedData;
    }

    /**
     * base64_decode data and then decrypt it
     *
     * @param string $data Base64-encoded data to decrypt
     * @return string Decrypted data
     */
    public function base64Decrypt($data)
    {
        return $this->decrypt(base64_decode($data));
    }
}
