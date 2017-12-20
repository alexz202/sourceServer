<?php
/**
 * Created by PhpStorm.
 * User: alexzhu
 * Date: 2017/12/13
 * Time: 下午2:13
 */
use config\Errorcode;
use file\Upload;

class AudioController extends ControllerBase
{
    public function testAction(){
        $config=Yaf_Registry::get('config')['application'];
        $this->checkSign($config);
        echo "test";
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