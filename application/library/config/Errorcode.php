<?php
/*
 * 错误码配置
 */
namespace config;

class Errorcode {
    public static $enum = array (
        'VERIFYFAIL'           => array('code'=>999,'msg'=>'签名错误'),
        'SUCCESS'           => array('code'=>1000,'msg'=>'成功'),
        'SYSTEM_ERROR'      => array('code'=>1001,'msg'=>'系统错误'),
        'TOKEN_VERIFY_ERROR'       => array('code'=>1002,'msg'=>'token校验失败'),
        'MSG_SIGN_ERROR'        =>array('code'=>1003,'msg'=>'签名校验失败') ,
        'PARAMS_ERROR'          =>array('code'=>1004,'msg'=>'参数错误'),
        'MSG_PARAMS_LESS'       => array('code'=>1005,'msg'=>'参数缺失'),

        'NOT_GET_FILE'=>array('code'=>2001,'msg'=>'未接受到文件信息'),
        'FILE_TYPE_INVAILD'=>array('code'=>2002,'msg'=>'文件信息类型非法'),
        'FILE_TYPE_IMAGE_ERROR'=>array('code'=>2003,'msg'=>'类型错误，只能上传图片'),

    );
}