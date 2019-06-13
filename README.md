# Pikirasa

[![Build Status](https://travis-ci.org/vlucas/pikirasa.svg?branch=master)](https://travis-ci.org/vlucas/pikirasa)
[![codecov](https://codecov.io/gh/vlucas/pikirasa/branch/master/graph/badge.svg)](https://codecov.io/gh/vlucas/pikirasa)

Easy PKI public/private RSA key encryption using the OpenSSL extension.

## What's up with the name?

Pikirasa is just "PKI RSA" with a few exta vowels thrown in. Also, I created
this project late on a Friday night, so I just couldn't bring myself to name it
anything serious.

## What is this for?

Pikirasa is very lightweight wrapper around PHP's OpenSSL extension for
encrypting and decrypting data with a known public/private key pair. It requires
that you have the [OpenSSL extension](http://php.net/openssl) installed, and
that your certificates have already been generated.

_Pikirasa is not a general purpose or all-encompassing encryption library_. If
you need more encryption options or maximum system compatability, take a look at
[phpseclib](https://github.com/phpseclib/phpseclib).

## Installation

Just fire up Composer!

```bash
composer require vlucas/pikirasa
```

## Example Usage

All you need is the full path to your public and/or private key files:

```php
$rsa = new Pikirasa\RSA('path/to/public.pem', 'path/to/private.pem');

$data = 'abc123';
$encrypted = $rsa->encrypt($data);
$decrypted = $rsa->decrypt($encrypted);
var_dump($decrypted); // 'abc123'
```

Under the hood, Pikirasa will make these paths file streams, and you may use any
file stream directly instead :

```php
$rsa = new Pikirasa\RSA('file:///absolute/path/to/public.pem', 'file://relative/path/to/private.pem');

$data = 'abc123';
$encrypted = $rsa->encrypt($data);
$decrypted = $rsa->decrypt($encrypted);
var_dump($decrypted); // 'abc123'
```

You can also use the string contents of your public and private keys :

```php
$publicKey = '
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA7o9A47JuO3wgZ/lbOIOs
Xc6cVSiCMsrglvORM/54StFRvcrxMi7OjXD6FX5fQpUOQYZfIOFZZMs6kmNXk8xO
hgTmdMJcBWolQ85acfAdWpTpCW29YMvXNARUDb8uJKAApsISnttyCnbvp7zYMdQm
HiTG/+bYaegSXzV3YN+Ej+ZcocubUpLp8Rpzz+xmXep3BrjBycAE9z2IrrV2rlwg
TTxU/B8xmvMsToBQpAbe+Cv130tEHsyW4UL9KZY1M9R+UHFPPmORjBKxSZvjJ1mS
UbUYN6PmMry35wCaFCfQoyTDUxBfxTGYqjaveQv4sxx0uvoiLXHt9cAm5Q8KJ+8d
FwIDAQAB
-----END PUBLIC KEY-----
';

$privateKey = '
-----BEGIN RSA PRIVATE KEY-----
MIIEpAIBAAKCAQEA7o9A47JuO3wgZ/lbOIOsXc6cVSiCMsrglvORM/54StFRvcrx
Mi7OjXD6FX5fQpUOQYZfIOFZZMs6kmNXk8xOhgTmdMJcBWolQ85acfAdWpTpCW29
YMvXNARUDb8uJKAApsISnttyCnbvp7zYMdQmHiTG/+bYaegSXzV3YN+Ej+Zcocub
UpLp8Rpzz+xmXep3BrjBycAE9z2IrrV2rlwgTTxU/B8xmvMsToBQpAbe+Cv130tE
HsyW4UL9KZY1M9R+UHFPPmORjBKxSZvjJ1mSUbUYN6PmMry35wCaFCfQoyTDUxBf
xTGYqjaveQv4sxx0uvoiLXHt9cAm5Q8KJ+8dFwIDAQABAoIBAHkWS3iHy/3zjjtY
TV4NL8NZqO5splGDuqXEMbKzenl3b8cnKHAxY/RVIQsh3tZb9CV8P/Lfj1Fi+nLt
a7mAXWcXO6aONMkmzI1zQ2NL3opoxTRc+GAWd0BW5hcoMBK1CD+ciHkLqAH5xsFc
UFxSc5qfTkb79GMlQZYD/Hk2WwHyj7hAkyxip4ye1EOnH5h8H7vIUjwp+H6Rmt5w
FTiVJbokhzwiczChUJVWgnowegL/qFV+yNfHGGKqVdIQfKdCsHR6jAuKCww5QniN
qDEi/M2Az0R4qfVmf38uMvOJTWaxp08JV4qRyNdh6hhbj+nY1EZ8haOiC7tjz2mJ
XqqKQfkCgYEA95yb5ezTBF4Pbr589OnU6VFdM88BCrKKvSWE8D1fzZZTsXur5k/x
cOwfio4RkmJwMnjuzZN6nvL5QddfcmPWQAoepHR8eA9yhIz57YWgrqE9ZXI8DgMy
SFuy5EkV5vudjDIr7kBXaGuUh3ErZfglyrV/rUfydGdTWyY8phMq/6MCgYEA9qQj
7kb5uyU8nrXoDqKPpy6ijEpVilgy4VR7RuB2vMh74wKI1QQYED+PxfcHe5RP8WGF
Bl+7VnmrGka4xJWeN7GKW4GRx5gRAzg139DXkqwPlXyM3ZR3pLd8wtbxTmJrcPby
A6uNRhGPpuyhDs5hx9z6HvLoCs+O0A9gDaChM/0CgYEAycRguNPpA2cOFkS8l+mu
p8y4MM5eX/Qq34QiNo0ccu8rFbXb1lmQOV7/OK0Znnn+SPKITRX+1mTRPZidWx4F
aLuWSpXtEvwrad1ijuzTiVk0KWUTkKuEHrgyJplzcnvX3nTHnWXqk9kN9+v83CN/
0BVji7TT2YyUvPKEeyOlZxcCgYABFm42Icf+JEblKEYyslLR2OnMlpNT/dmTlszI
XjsH0BaDxMIXtmHoyG7434L/74J+vQBaK9fmpLi1b/RmoYZGFplWl/atm6UPj5Ll
PsWElw+miBsS6xGv/0MklNARmWuB3wToMTx5P6CTit2W9CAIQpgzxLxzN8EYd8jj
pn6vfQKBgQCHkDnpoNZc2m1JksDiuiRjZORKMYz8he8seoUMPQ+iQze66XSRp5JL
oGZrU7JzCxuyoeA/4z36UN5WXmeS3bqh6SinrPQKt7rMkK1NQYcDUijPBMt0afO+
LH0HIC1HAtS6Wztd2Taoqwe5Xm75YW0elo4OEqiAfubAC85Ec4zfxw==
-----END RSA PRIVATE KEY-----
';

$rsa = new Pikirasa\RSA($publicKey, $privateKey);

$data = 'abc123';
$encrypted = $rsa->encrypt($data);
$decrypted = $rsa->decrypt($encrypted);
var_dump($decrypted); // 'abc123'
```

### Creating keys

Don't have key files already? No problem - you can simply create new ones :

```php
$rsa = new Pikirasa\RSA('path/to/nonexistent_public.pem', 'path/to/nonexistent_private.pem');
$rsa->create();  // creates new keys in the new key files

$data = 'abc123';
$encrypted = $rsa->encrypt($data);
$decrypted = $rsa->decrypt($encrypted);
var_dump($decrypted); // 'abc123'
```

Need a key size other than the default of 2048 bits? Simply pass the size you
need as the first parameter of `$rsa->create()`.

Pikirasa won't overwrite existing key files unless you pass `true` to the second
parameter of `$rsa->create()`.

If you prefer to work with key strings over key files, you can create keys that
way, too :

```php
$rsa = new Pikirasa\RSA(null, null);
$rsa->create();  // creates new keys as strings

$data = 'abc123';
$encrypted = $rsa->encrypt($data);
$decrypted = $rsa->decrypt($encrypted);
var_dump($decrypted);
          // 'abc123'
var_dump($rsa->getPublicKeyFile());
          // -----BEGIN PUBLIC KEY-----
          // MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA7o9A47JuO3wgZ/lbOIOs
          // Xc6cVSiCMsrglvORM/54StFRvcrxMi7OjXD6FX5fQpUOQYZfIOFZZMs6kmNXk8xO
          // hgTmdMJcBWolQ85acfAdWpTpCW29YMvXNARUDb8uJKAApsISnttyCnbvp7zYMdQm
          // HiTG/+bYaegSXzV3YN+Ej+ZcocubUpLp8Rpzz+xmXep3BrjBycAE9z2IrrV2rlwg
          // TTxU/B8xmvMsToBQpAbe+Cv130tEHsyW4UL9KZY1M9R+UHFPPmORjBKxSZvjJ1mS
          // UbUYN6PmMry35wCaFCfQoyTDUxBfxTGYqjaveQv4sxx0uvoiLXHt9cAm5Q8KJ+8d
          // FwIDAQAB
          // -----END PUBLIC KEY-----
var_dump($rsa->getPrivateKeyFile());
          // -----BEGIN RSA PRIVATE KEY-----
          // MIIEpAIBAAKCAQEA7o9A47JuO3wgZ/lbOIOsXc6cVSiCMsrglvORM/54StFRvcrx
          // Mi7OjXD6FX5fQpUOQYZfIOFZZMs6kmNXk8xOhgTmdMJcBWolQ85acfAdWpTpCW29
          // YMvXNARUDb8uJKAApsISnttyCnbvp7zYMdQmHiTG/+bYaegSXzV3YN+Ej+Zcocub
          // UpLp8Rpzz+xmXep3BrjBycAE9z2IrrV2rlwgTTxU/B8xmvMsToBQpAbe+Cv130tE
          // HsyW4UL9KZY1M9R+UHFPPmORjBKxSZvjJ1mSUbUYN6PmMry35wCaFCfQoyTDUxBf
          // xTGYqjaveQv4sxx0uvoiLXHt9cAm5Q8KJ+8dFwIDAQABAoIBAHkWS3iHy/3zjjtY
          // TV4NL8NZqO5splGDuqXEMbKzenl3b8cnKHAxY/RVIQsh3tZb9CV8P/Lfj1Fi+nLt
          // a7mAXWcXO6aONMkmzI1zQ2NL3opoxTRc+GAWd0BW5hcoMBK1CD+ciHkLqAH5xsFc
          // UFxSc5qfTkb79GMlQZYD/Hk2WwHyj7hAkyxip4ye1EOnH5h8H7vIUjwp+H6Rmt5w
          // FTiVJbokhzwiczChUJVWgnowegL/qFV+yNfHGGKqVdIQfKdCsHR6jAuKCww5QniN
          // qDEi/M2Az0R4qfVmf38uMvOJTWaxp08JV4qRyNdh6hhbj+nY1EZ8haOiC7tjz2mJ
          // XqqKQfkCgYEA95yb5ezTBF4Pbr589OnU6VFdM88BCrKKvSWE8D1fzZZTsXur5k/x
          // cOwfio4RkmJwMnjuzZN6nvL5QddfcmPWQAoepHR8eA9yhIz57YWgrqE9ZXI8DgMy
          // SFuy5EkV5vudjDIr7kBXaGuUh3ErZfglyrV/rUfydGdTWyY8phMq/6MCgYEA9qQj
          // 7kb5uyU8nrXoDqKPpy6ijEpVilgy4VR7RuB2vMh74wKI1QQYED+PxfcHe5RP8WGF
          // Bl+7VnmrGka4xJWeN7GKW4GRx5gRAzg139DXkqwPlXyM3ZR3pLd8wtbxTmJrcPby
          // A6uNRhGPpuyhDs5hx9z6HvLoCs+O0A9gDaChM/0CgYEAycRguNPpA2cOFkS8l+mu
          // p8y4MM5eX/Qq34QiNo0ccu8rFbXb1lmQOV7/OK0Znnn+SPKITRX+1mTRPZidWx4F
          // aLuWSpXtEvwrad1ijuzTiVk0KWUTkKuEHrgyJplzcnvX3nTHnWXqk9kN9+v83CN/
          // 0BVji7TT2YyUvPKEeyOlZxcCgYABFm42Icf+JEblKEYyslLR2OnMlpNT/dmTlszI
          // XjsH0BaDxMIXtmHoyG7434L/74J+vQBaK9fmpLi1b/RmoYZGFplWl/atm6UPj5Ll
          // PsWElw+miBsS6xGv/0MklNARmWuB3wToMTx5P6CTit2W9CAIQpgzxLxzN8EYd8jj
          // pn6vfQKBgQCHkDnpoNZc2m1JksDiuiRjZORKMYz8he8seoUMPQ+iQze66XSRp5JL
          // oGZrU7JzCxuyoeA/4z36UN5WXmeS3bqh6SinrPQKt7rMkK1NQYcDUijPBMt0afO+
          // LH0HIC1HAtS6Wztd2Taoqwe5Xm75YW0elo4OEqiAfubAC85Ec4zfxw==
          // -----END RSA PRIVATE KEY-----
```

### Using Keys with a Passphrase

The `Pikirasa\RSA` class constructor accepts an optional 3rd parameter if your
private key is protected with a password.

```php
$rsa = new Pikirasa\RSA($publicKey, $privateKey, 'certificate_password');

$data = 'abc123';
$encrypted = $rsa->encrypt($data);
$decrypted = $rsa->decrypt($encrypted);
var_dump($decrypted); // 'abc123'
```

This approach also works when creating new keys that should be password
protected :

```php
$rsa = new Pikirasa\RSA($publicKey, $privateKey, 'certificate_password');
$rsa->create(); // creates new keys, with the private key password-protected

$data = 'abc123';
$encrypted = $rsa->encrypt($data);
$decrypted = $rsa->decrypt($encrypted);
var_dump($decrypted); // 'abc123'

$rsa2 = new Pikirasa\RSA($publicKey, $privateKey);
$decrypted = $rsa2->decrypt($encrypted); // Throws `Pikirasa\Exception` for bad/missing password
```

### Working with base64-encoded strings

A common pattern if you want to deal with plain strings rather than binary data
is to encode encryped data with base64. If you need to do that, both `encrypt`
and `decrypt` have a base64 counterpart you can use :

```php
$rsa = new Pikirasa\RSA($publicKey, $privateKey);

$data = 'abc123';
$encrypted = $rsa->base64Encrypt($data);
$decrypted = $rsa->base64Decrypt($encrypted);
var_dump($decrypted); // 'abc123'
```
