<?php
class AESEncryption{
    private $cipher;
    public $key;
    private $iv;

    public function __construct($cipher = 'aes-128-cbc', $key = null) {
        $this->cipher = $cipher;
        if ($key === null) {
            $this->key = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->cipher));
        } else {
            $this->key = $key;
        }
        $this->iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->cipher));
    }

    public function encrypt($data) {
        $enc = openssl_encrypt($data, $this->cipher, $this->key, OPENSSL_RAW_DATA, $this->iv);
        return base64_encode($this->iv . $enc);
    }

    public function decrypt($data) {
        $decoded = base64_decode($data);
        $iv_dec = substr($decoded, 0, 16);
        $encrypted_data = substr($decoded, 16);
        return openssl_decrypt($encrypted_data, $this->cipher, $this->key, OPENSSL_RAW_DATA, $iv_dec);
    }

}
?>