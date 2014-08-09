<?php
namespace Pikirasa;

class RSA
{
    public function encrypt($key, $data)
    {
        // Load public key
        $publicKey = openssl_pkey_get_public($key);
        if (!$publicKey) {
            throw new Exception("OpenSSL: Unable to get public key for encryption");
        }

        $success = openssl_public_encrypt($data, $encryptedData, $publicKey);
        openssl_free_key($publicKey);
        if (!$success) {
            throw new Exception("Encryption failed. Ensure you are using a PUBLIC key.");
        }

        return $encryptedData;
    }

    public function decrypt($key, $data)
    {
        $privateKey = openssl_pkey_get_private($key);
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
