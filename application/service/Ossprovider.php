<?php
/**
 * Created by PhpStorm.
 * User: alexzhu
 * Date: 2017/12/13
 * Time: 下午4:02
 */
namespace service;
use OSS\OssClient;
use config\Aliyunoss;

class Ossprovider {
    private $config=null;
    private $ossClient=null;
    private $bucket=null;

    public function __construct($bucket=null)
    {
        $this->config= Aliyunoss()->config;
        if(!$bucket){
            $this->bucket=$this->config['oss_bucket'];
        }
        $this->ossClient=new  OssClient($this->config['oss_access_id'],$this->config['oss_access_key'],$this->config['oss_endpoint']);
    }



    /*
     * 上传内存数据
     */
    public function putObject($object, $content, $options = NULL){
        return $this->ossClient->putObject($this->bucket, $object, $content, $options );
    }


    /*
     * 上传文件
     */
    public function uploadFile($object, $file, $options = NULL){
        return $this->ossClient->uploadFile($this->bucket, $object, $file, $options);
    }



    /*
     * 分片上传文件
     */
    public function multiuploadFile($object, $file, $options = null){
        return $this->ossClient->multiuploadFile($this->bucket, $object, $file, $options = null);
    }


}