<?php
/**
 * Created by PhpStorm.
 * User: zhualex
 * Date: 17/2/15
 * Time: 下午5:30
 */


function _responseReturn($code=1,$data=[],$msg=''){
    $response_arr=array(
        'code'=>$code,
        'msg'=>$msg,
        'data'=>$data
    );
    echo json_encode($response_arr,true);
}