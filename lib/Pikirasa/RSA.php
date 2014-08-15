<?php
namespace Pikirasa;

class RSA
{
    protected $publicKeyFile;
    protected $privateKeyFile;
    protected $password;

    public function __construct($publicKeyFile, $privateKeyFile = null, $password = null)
    {
        $this->publicKeyFile = $publicKeyFile;
        $this->privateKeyFile = $privateKeyFile;
        $this->password = $password;
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
}
