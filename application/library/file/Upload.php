<?php
/**
 * Created by PhpStorm.
 * User: zhualex
 * Date: 17/2/15
 * Time: 下午6:24
 */
namespace file;
use util\Image;
use service\Ffmpeg;

class Upload{
    private $allowFileType=['gif','jpg','jpeg','bmp','png','opus','txt','mp3'];

    public $thumb   =  false;
    // 缩略图最大宽度
    public $thumbMaxWidth='60,150,315';
    // 缩略图最大高度
    public $thumbMaxHeight='80,150,420';
    // 缩略图前缀
    public $thumbPrefix   =  'small_,thumb_,big_';
    public $thumbSuffix  =  '';
    // 缩略图保存路径
    public $thumbPath = '/thumb/';
    /*
     * 文件保存
     */    
    public  function save($file,&$link,$config,$is_random_name=0){
        $link=array();
        $tmp_name=$file['tmp_name'];
        $ext=$this->getFileExt($file);
        if($is_random_name==1){
            $real_name=$this->randName($ext);
        }
        else
            $real_name=$file['name'];
        //自定义目录
        if(!empty($file['designated_path'])){
            $_path=$config['upload_folder'].DS.$file['designated_path'];
            $aim_name=$_path.DS.$real_name;
            $_path=$this->filterDoubleS($_path);
            $this->mkDirs($_path);
        }
        else
            $aim_name=$config['upload_folder'].DS.$real_name;

        $res= move_uploaded_file($tmp_name,$aim_name);
        if($res){
            $link =
                array(
                    'fileUrl'=>  $config['server_name'] . DS . ltrim($aim_name,'/'),
                    'fileName'=>$real_name,
                    'ext'=>$ext,
                    'fileUri'=> $this->filterUri($aim_name,$config),
                );

            if($this->thumb==true){
                //生成缩略图
               $link['Thumb']= $this->makeThumb($aim_name,$real_name,$config,$file['designated_path']);
            }else{

                if($ext=='opus'){
                    $type='mp3';
                    $name=substr($real_name,0,-5).'.'.$type;
                    $encodeName=substr($aim_name,0,-5).'.'.$type;
                    Ffmpeg::fEncode($aim_name,$encodeName,$type);
                    $link['mp3Url']=$this->filterUri($encodeName,$config);
                    $link['mp3Name']=$name;
                }
            }
            return true;

        }else{
            return false;
        }

    }

    /*
     * base64图片数据流
     */
    public function base64Save($filestr,&$link,$config,$is_random_name=1){
        $link=array();
        $base_arr=explode(',',$filestr);
        $image_info=$base_arr[0];
        //获取文件后缀
        preg_match("/data:image\/(.*);base64/i",$image_info,$output);
        $ext=$output[1];
        if(empty($ext))
            $ext="png";
        if($is_random_name==1){
            $real_name=$this->randName($ext);
        }
        $image=base64_decode($base_arr[1]);

        $uploadDir = $config['upload_folder'].DS.date("Y-m-d");
        $dir = $this->mkDirs($uploadDir);
        if(!$dir){
          return false;
        }
        $aim_name=$uploadDir.DS.$real_name;

        $res=file_put_contents($aim_name,$image);

        if($res) {
            if($this->thumb==true){
                //生成缩略图
                $link= $this->makeThumb($aim_name,$real_name,$config);
            }
            $link[] = $config['server_name'] . DS . $uploadDir . DS . $real_name;
            return true;
        }
        else
            return false;

    }

    /*
     * 去掉根目录 与上传的目录结构一样
     */
    private function filterUri($uri,$config){
        return str_replace($config['upload_folder'],'',$uri);
    }


    /*
  * base64数据流
  */
    public function streamSaveFile($filestr,&$link,$config,$ext,$is_random_name=1){
        $link=array();
        //获取文件后缀
        if($is_random_name==1){
            $real_name=$this->randName($ext);
        }

       // $uploadDir = $config['upload_folder'].DS.date("Y-m-d");
        $uploadDir = $config['upload_folder'];
        $dir = $this->mkDirs($uploadDir);
        if(!$dir){
            return false;
        }
        $aim_name=rtrim($uploadDir,'/').DS.$real_name;

        $res=file_put_contents($aim_name,$filestr,true);

        if($res) {
            $link =
                array(
                    'fileUrl'=>  $config['server_name'] . DS .trim($uploadDir,'/') . DS . $real_name,
                    'fileName'=>$real_name,
                    'ext'=>$ext,
                    'fileUri'=> $this->filterUri($aim_name,$config),
                );

            if($ext=='opus'){
                $type='mp3';
                $name=substr($real_name,0,-5).'.'.$type;
                $encodeName=substr($aim_name,0,-5).'.'.$type;
                Ffmpeg::fEncode($aim_name,$encodeName,$type);
                $link['mp3Uri']=$this->filterUri($encodeName,$config);
                $link['mp3Name']=$name;
                $link['mp3Url']= $config['server_name'] . DS.ltrim($encodeName,'/');
                $link['ext']=$type;
            }
            return true;
        }
        else
            return false;
    }


    /*;
     * 获取文件后缀名
     */
    public function getFileExt($file){
       return $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    }

    public function checkImage($file){
        $ext=$this->getFileExt($file);
        if(in_array(strtolower($ext),$this->allowFileType)){
            return true;
        }else{
            return false;
        }
    }

    /*
     * private
     */

    private function randName($ext){
        $file_name = sha1(time() . mt_rand(111111, 999999)) . "." . $ext;
        return $file_name;
    }

    private function makeThumb($filename,$real_name,$config,$designated_path=''){
        $link=array();
        if($this->thumb){
            $thumbMaxWidth=explode(',',$this->thumbMaxWidth);
            $thumbMaxHeight=explode(',',$this->thumbMaxHeight);
            $thumbPix=explode(',',$this->thumbPrefix);
            $i=0;
            foreach($thumbMaxWidth as $v){
                if(!empty($designated_path)){
                    $thumbDir =$config['upload_folder']. DS.$designated_path.$this->thumbPath;
                }else{
                    $thumbDir = $config['upload_folder'].$this->thumbPath.date('Y-m-d');
                }
                $thumb_filename=$thumbPix[$i].$real_name;
                $url=$config['server_name'] . DS . $thumbDir.DS.$thumb_filename;
                $uri=$thumbDir.DS.$thumb_filename;
                $dir=$this->filterDoubleS($thumbDir);
                $dir = $this->mkDirs($thumbDir);
                if(!$dir){
                    continue;
                }
                $res=image::thumb($filename,$uri,'',intval($v),intval($thumbMaxHeight[$i]));
                if($res)
                    $link[]=array('fileUrl'=>$url,'fileUri'=>$this->filterUri($uri,$config),'fileName'=>$thumb_filename);
                $i++;
            }
        }
        return $link;
    }


    private function filterDoubleS($url){
        return str_replace("//", "/", $url);    
    }


    private function mkDirs($dir){
        if(!is_dir($dir)){
            if(!@mkdir($dir,0777,true)){
                return false;
            }
        }
        return true;
    }
}