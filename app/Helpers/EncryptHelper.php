<?php

namespace App\Helpers;

/**
 * Class EncryptHelper - SHA265 encryptor
 * @package App\Helpers
 */
class EncryptHelper
{
    /**
     * @param string $string
     * @return string
     */
    public static function encrypt(string $string): string
    {
        $pub = <<<PUBKEY
-----BEGIN PUBLIC KEY-----
MFwwDQYJKoZIhvcNAQEBBQADSwAwSAJBAMygnZcyfPcb46hy2MGQHR8fo4FsyOJG
0lJFgNwVlfEPj+6KZLawz1OVam3SYO00wivT1goPnpn3GA8HIVEM5Q8CAwEAAQ==
-----END PUBLIC KEY-----
PUBKEY;
        $pk  = openssl_get_publickey($pub);
        openssl_public_encrypt($string, $encrypted, $pk);
        return chunk_split(base64_encode($encrypted));
    }

    /**
     * @param string $string
     * @return string
     */
    public static function decrypt(string $string): string
    {
        $key = <<<PRIVATEKEY
-----BEGIN RSA PRIVATE KEY-----
MIIBOgIBAAJBAMygnZcyfPcb46hy2MGQHR8fo4FsyOJG0lJFgNwVlfEPj+6KZLaw
z1OVam3SYO00wivT1goPnpn3GA8HIVEM5Q8CAwEAAQJAFyu0zJMl/WFGFSP0EW79
LTIK9LPZx22XkUNXhRRYpzQVE1Yw7LVPM0F4GTug93eEcDjwQFeLJdLS5VXQD4H3
yQIhAOlK9Fav2X3U3LVR3WwX9/ipBjwDeqvf+9h/whzsY9kVAiEA4ItlD3qjcD8e
+lq3PCJjzmJLU2a7MCcGx6TFZnTNxpMCIH/mghLMkfa0rtQRr81FTsPbFvnsBmMY
h2Bomql0yEEJAiBhQp/Eo6dVgFHHuTFzH6ZBh/v+pRnhkpXyNeG3LlLPdwIhAKtI
+NrDWqk3vHMvb/UsbTdt7b5BAu/NBcK6a4Q1wOVi
-----END RSA PRIVATE KEY-----
PRIVATEKEY;
        $pk  = openssl_get_privatekey($key);
        openssl_private_decrypt(base64_decode($string), $out, $pk);
        return $out;
    }
}
