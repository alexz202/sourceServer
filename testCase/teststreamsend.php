<?php
/**
 * Created by PhpStorm.
 * User: alexzhu
 * Date: 2017/12/14
 * Time: 下午3:19
 */

/** php 发送流文件
 * @param  String  $url  接收的路径
 * @param  String  $file 要发送的文件
 * @return boolean
 */
function sendStreamFile($url, $file){

    if(file_exists($file)){

        $opts = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'content-type:application/x-www-form-urlencoded',
                'content' => file_get_contents($file)
            )
        );

        $context = stream_context_create($opts);
        $response = file_get_contents($url, false, $context);
        $ret = json_decode($response, true);
        return $ret['success'];

    }else{
        return false;
    }

}

$ret = sendStreamFile('http://lc.source.dev.zejicert.cn/Audio/fileStream?sign=123&ext=opus', 'tt16.opus');
var_dump($ret);
?>