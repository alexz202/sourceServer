<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/9
 * Time: 15:02
 */
namespace service;

use config\Aes as AesConfig;
class Aes {

    public $iv = null;
    public $key = null;
    public $bit = 128;
    private $cipher;
    private $config;

    public function __construct()
    {
        $this->config = (new AesConfig())->config;
        $this->bit = $this->config['bit']; //配置加密位
        $this->key = $this->config['key']; //配置秘钥
        $this->iv  = $this->config['iv']; //配置向量
        $this->mode = $this->config['mode']; //配置加密方式
        switch($this->bit) {
            case 192:$this->cipher = MCRYPT_RIJNDAEL_192; break;
            case 256:$this->cipher = MCRYPT_RIJNDAEL_256; break;
            default: $this->cipher = MCRYPT_RIJNDAEL_128;
        }
        switch($this->mode) {
            case 'ecb':$this->mode = MCRYPT_MODE_ECB; break;
            case 'cfb':$this->mode = MCRYPT_MODE_CFB; break;
            case 'ofb':$this->mode = MCRYPT_MODE_OFB; break;
            case 'nofb':$this->mode = MCRYPT_MODE_NOFB; break;
            default: $this->mode = MCRYPT_MODE_CBC;
        }
    }
    //加密
    public function encrypt($data)
    {
        $size = mcrypt_get_block_size($this->cipher, $this->mode);
       // $data = $this->pkcs5_pad($data, $size);
        return base64_encode(
            mcrypt_encrypt(
                $this->cipher, $this->key, $data, $this->mode, $this->iv
            )
        );
    }
    //解密
    public function decrypt($data)
    {
        $data = mcrypt_decrypt(
            $this->cipher, $this->key, base64_decode($data), $this->mode,
            $this->iv);
        return rtrim($data,"\0");
        //return $this->pkcs5_depad($data);
    }
    private function pkcs5_pad ($text, $blocksize)
    {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }
    private function pkcs5_depad($text){
        $dec_s = strlen($text);
        $padding = ord($text[$dec_s-1]);
        return substr($text, 0, -$padding);
    }
}