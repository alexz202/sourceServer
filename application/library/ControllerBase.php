<?php
/**
 * Created by PhpStorm.
 * User: zjy202
 * Date: 2017/12/13
 * Time: 11:38
 */

use config\Errorcode;

class ControllerBase extends Yaf_Controller_Abstract
{
    protected $key = "123321!@#";

    public function checkSign($config)
    {
        $data = array();

        if($config['checkSing']==1){
            if (isset($_GET['sign'])) {
                $data = $_GET;
                unset($data['sign']);
                if ($_GET['sign'] == $this->makeSign($data)) {
                    return true;
                } else {
                    _responseReturn(Errorcode::$enum['VERIFYFAIL']['code'], []);
                }
            } else {
                _responseReturn(Errorcode::$enum['VERIFYFAIL']['code'], []);
            }
        }else
            return true;

    }


    private function makeSign($data)
    {
        $signKey = $this->key;
        ksort($data);
        foreach ($data as $key => $value) {
            if (is_array($value)) $data[$key] = json_encode($value, \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES);
        }
         $sign = md5(implode($signKey, $data));
        return $sign;
    }
}
