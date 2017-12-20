<?php
/**
 * @name Bootstrap
 * @author zhualex
 * @desc 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
 * @see http://www.php.net/manual/en/class.yaf-bootstrap-abstract.php
 * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */
class Bootstrap extends Yaf_Bootstrap_Abstract {

	/* 初始项 */
	public function _initConfig() {
		//把配置保存起来
		$arrConfig = Yaf_Application::app()->getConfig();
		Yaf_Registry::set('config', $arrConfig);

		//关闭自动加载模板
		Yaf_Dispatcher::getInstance()->autoRender(false);

		//自动加载
		spl_autoload_register('Bootstrap::autoLoader', true, true);
	}

	/* 设置路由
    public function _initRoute(Yaf_Dispatcher $dispatcher) {
        $router = Yaf_Dispatcher::getInstance()->getRouter();
        $router->addConfig(Yaf_Registry::get("config")->routes);
    }
    */
	/* 错误设置 */
	static function error_catch() {
		$error = error_get_last();
		if($error){
			echo "{$error['message']} in file: {$error['file']} on line {$error['line']}<br>";
		}
	}

	public function _initErrors() {
		if(Yaf_Registry::get("config")->application->showErrors){
			//register_shutdown_function("Bootstrap::error_catch");
			set_error_handler(array($this,'handleError'), E_ALL);
		}else{
			//error_reporting (0);
		}

	}

	/**
	 * 自定义自动加载函数
	 * @param unknown $class
	 * @return boolean
	 */
	final public static function autoLoader($class){
		$class = str_replace('\\', DS, $class);
		$classpath = APP_PATH . DS . $class . '.php';
		if (is_file($classpath)) {
			include $classpath;
		}
		return true;
	}

	/* 得到并报告一个系统错误 */
	function handleError($errorNo, $message, $filename, $lineNo) {
		if(error_reporting () != 0) {
			$type = 'error';
			switch ($errorNo) {
				case E_WARNING :
					$type = 'warning';
					break;
				case E_NOTICE :
					$type = 'notice';
					break;
			}
			exit("PHP $type in file $filename($lineNo) $message <br>");
			//throw new Exception("PHP $type in file $filename($lineNo) $message", 0);
		}
	}


	public function _initCommonFunctions(){
		Yaf_Loader::import(Yaf_Application::app()->getConfig()->application->directory . '/common/functions.php');
	}

}

