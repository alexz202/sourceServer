<?php
/**
 * Created by PhpStorm.
 * User: zhualex
 * Date: 17/2/15
 * Time: 下午4:55
 * 上传文件
 */

use config\Errorcode;
use file\Upload;


class UploadController extends ControllerBase {

    public function __initialize(){
       // Yaf_Dispatcher::getInstance()->disableView();
    }

    /*
     *  上传图片
     */
    public  function imageAction(){
        $data=array();
        $link='';
        if(!empty($_FILES)){
            $is_random_name=isset($_GET['is_random_name'])?$_GET['is_random_name']:0;
            $designated_path=isset($_GET['designated_path'])?$_GET['designated_path']:'';
            $makeThumb=isset($_GET['makeThumb'])?intval($_GET['makeThumb']):0;
            $file=$_FILES['filename'];
            $file['designated_path']=$designated_path;
            $upload=new Upload();
            if($makeThumb==1)
               $upload->thumb=true; 
            //check image
            if($upload->checkImage($file)){
                $config=Yaf_Registry::get('config')['application'];
                $res=$upload->save($file,$link,$config,$is_random_name);
                if($res){
                    $data['link']=$link;
                    _responseReturn(Errorcode::$enum['SUCCESS']['code'],$data);
                }
                else
                    _responseReturn(Errorcode::$enum['SYSTEM_ERROR']['code']);
            }else{
                _responseReturn(Errorcode::$enum['FILE_TYPE_IMAGE_ERROR']['code']);
            }
        }else {
            _responseReturn(Errorcode::$enum['NOT_GET_FILE']['code']);
        }
    }


    /*
     *上传base64图片字符串
     */
    public function avatarAction(){
        $data=array();
        $link='';
        $config=Yaf_Registry::get('config')['application'];
        $filestr=file_get_contents("php://input");
        if(!$filestr){
            _responseReturn(Errorcode::$enum['SYSTEM_ERROR']['code']);
        }
        $upload=new Upload();
        $upload->thumb=true;
        $res=$upload->base64Save($filestr,$link,$config,1);
        if($res){
            $data['link']=$link;
            _responseReturn(Errorcode::$enum['SUCCESS']['code'],$data);
        }
        else
            _responseReturn(Errorcode::$enum['SYSTEM_ERROR']['code']);
    }


    public function fileAction(){
        $config=Yaf_Registry::get('config')['application'];
        $this->checkSign($config);
        if(!empty($_FILES)){
            $file=$_FILES['filename'];
            $is_random_name=isset($_GET['is_random_name'])?$_GET['is_random_name']:0;
            $designated_path=isset($_GET['designated_path'])?$_GET['designated_path']:'';
            $file=$_FILES['filename'];
            $file['designated_path']=$designated_path;
            $upload=new Upload();

            $res=$upload->save($file,$link,$config,$is_random_name);
            if($res){
                $data['link']=$link;
                _responseReturn(Errorcode::$enum['SUCCESS']['code'],$data);
            }
        }else
            _responseReturn(Errorcode::$enum['NOT_GET_FILE']['code']);
    }

    public function fileStreamAction(){
        $ext=isset($_GET['ext'])?$_GET['ext']:null;
        if(empty($ext)){
            _responseReturn(Errorcode::$enum['NOT_GET_FILE']['code']);
        }

        $config=Yaf_Registry::get('config')['application'];
        $this->checkSign($config);
        $data=array();
        $link='';
        $filestr=file_get_contents("php://input");
        if(!$filestr){
            _responseReturn(Errorcode::$enum['SYSTEM_ERROR']['code']);
        }
        $upload=new Upload();
        $res=$upload->streamSaveFile($filestr,$link,$config,$ext,1);
        if($res){
            $data['link']=$link;
            _responseReturn(Errorcode::$enum['SUCCESS']['code'],$data);
        }
        else
            _responseReturn(Errorcode::$enum['SYSTEM_ERROR']['code']);
    }
}