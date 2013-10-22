<?php
 /**
  * 系统入口 (index.php)
  *
  * @decription 整个系统的入口，启动框架phpframe
  *
  * @author Chen weihan <csq-3@163.com>  development < ubuntu-10.4 vim-7.3 nginx-0.8 php-5.39 mysql-5.1 >
  * @version v1.0
  * @copyright v1.0
  * @package public
  */
  
  /**
   * 设置运行环境参数
   * 
   * 编码，时区，错误报告 设置
   */
  header('Content-type:text/html;charset = utf-8');
  date_default_timezone_set('Asia/Shanghai');  
  error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
      
  /**
   * 访问路径常量
   */
  define('WEBDIR','/website/public');
  define('WEB_URL','http://127.0.0.1' . WEBDIR);
  define('APP_PATH',realpath(dirname(dirname(__FILE__))) . '/app');
  define('LIB_PATH',realpath(dirname(dirname(__FILE__))) . '/lib');
  define('CONFIG_PATH',realpath(dirname(dirname(__FILE__))) . '/config');
  define('WWW_PATH',realpath(dirname(__FILE__)));
  define('FRAME_PATH',realpath(dirname(dirname(__FILE__))));
  
  /**
   * 引入配置文件
   */
  require CONFIG_PATH . '/config.php';
     
  /**
   * 引入异常处理与自动载入类
   */  
  require LIB_PATH . '/frame/exception/FrameException.class.php';
  require LIB_PATH . '/frame/autoload/AutoLoad.class.php';
  /**
   *  统一管理框架级和代码级异常处理
   */
  try{
    
   /**
    * 启动自动加载类
    */
    AutoLoad::registerDir($autoDirConfig);
   
   /**
    * 启动run
    */
    $app = App::getInstance();
    $app->run();
    //throw new FrameException('msg',509);

  } catch (FrameException $e) {
     echo "<pre>";
     var_dump($e->errorMsg());
     echo "</pre>";  
  } catch (Exception $e) {
     echo "<pre>";
     var_dump($e);
     echo "</pre>";
  }
?>
