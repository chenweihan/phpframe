<?php
/**
 * 自动加载管理类
 *  
 * @description  对类载入得管理，方便按需加载。  
 * @author Chen weihan <csq-3@163.com>
 * @version v1.0
 * @copyright v1.0
 * @package frame
 */

/**
 * 自动加载类 AutoLoad
 */
class AutoLoad {
    
    /**
     * 私有变量 缓存自动载入目录 
     * @static
     * 
     */    
    static private $_path = array();
    
    /**
     * 注册需要自动载入目录，并启动自动注册函数
     * @static
     * @param {array} $path 数组
     */
    static public function registerDir($path) {
       self::$_path = $path;
       spl_autoload_register(array(self,'loadClass'));
    }
    
    /**
     * 目录判断，载入文件
     * @param {string} $class
     * @static
     * @todo exception 完善
     */       
    static private function loadClass($class) {
        
        
        $file = $class . '.class.php';         
        //echo "<br>".$file."<br>";
        //echo "<pre>";
        //var_dump(self::$_path);
        //echo "</pre>";
        foreach (self::$_path as $path ) {
           //if (file_exists($path.$file)){ 
           if (is_file($path.$file)) {
               require $path.$file;
           } 
         }  
    }


}
?>
