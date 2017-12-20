<?php
/**
 * Created by PhpStorm.
 * User: alexzhu
 * Date: 2017/12/14
 * Time: 下午1:28
 */
namespace service;
class Ffmpeg {
    public static function fEncode($originFileName,$aimFileName,$type='mp3',$bit=16){
        try{
            $command="ffmpeg -i $originFileName -ab $bit*1000 $aimFileName";
            system($command);
            return true;
        }catch (Exception $ex){
            return false;
        }
    }



}