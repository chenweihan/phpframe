<?php
/**
 * APP 启动
 *
 * @decription 系统启动App类，主要引入相关配置文件，加载基础类，框架资源管理控制
 * @version v1.0
 * @author Chen weihan <csq-3@163.com>
 * @copyright v1.0
 * @package frame
 * 
 * @example $app = App::getInstance; $app->run();
 *   
 */


/**
 * App类 单例模式
 *
 */
class App {
     
     /**
      * 单例私有变量
      * @var obj
      * @access private
      * @static
      */
     private static $_instance;

     /**
      * 私有构造函数
      */
     private function __construct(){}

     /**
      * 单例App 避免多次实例
      * @return obj App实例
      */  
     public static function getInstance() {
          if (!(self::$_instance instanceof self)) {
              self::$_instance = new self();
          }
          return self::$_instance;
      }

     /**
      * 单例避免克隆，保持类单一职责原则
      */    
     private function __clone(){}

     /**
      * 框架启动 run方法
      * 
      * @decription run启动
      * @example $app->run();
      * 
      */
     public function run() {
        //var_dump("run"); 
        $this->handleRequest();
     }

     /**
      * 命令处理
      */
     private function handleRequest() {
          
          $router  = new Router();	

     }

     /**
      * smarty模板的引入
      */
     private function tplSmarty() {
     
     }

     /**
      * plugin机制得引入
      */
     private function plugin() {
     
     }
}

?>
