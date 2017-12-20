<?php
date_default_timezone_set('PRC');
header("Access-Control-Allow-Origin: *");
header('Content-type: text/json; charset=utf-8');
///define('TIME_ZONE', 'Asia/Shanghai');
define('DS', DIRECTORY_SEPARATOR);
define('CURR_TIME', time());
define('ROOT_PATH', __DIR__);
define('APP_PATH', ROOT_PATH . DS . 'application');
define('APPLICATION_PATH', dirname(__FILE__));

$application = new Yaf_Application( APPLICATION_PATH . "/conf/application.ini");

$application->bootstrap()->run();
?>
