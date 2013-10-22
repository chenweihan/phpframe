<?PHP
/**
 * 系统配置文件
 *
 * @decription 系统各模块，组件，数据访问等配置
 * @author Chen weihan <csq-3@163.com>
 * @copyright v1.0
 * @version v1.0
 * @package config
 */

/**
 * 非单一入口页面，可以判断该常量，禁止非法直接调用
 */
define('PHPFRAME',true);

/**
 * 映射目录路径
 */
set_include_path(implode(PATH_SEPARATOR,array(
    APP_PATH . '/model/',
    APP_PATH . '/plug',
    LIB_PATH . '/frame/frontcontroller/'
  )
));

/**
 * 配置允许类自动加载目录
 */
$autoDirConfig = array (
    LIB_PATH . '/frame/frontcontroller/',
    LIB_PATH . '/frame/log/'
);

/**
 * 系统默认请求路径
 */
define('DEFAULT_PATH','home/index/index');


/**********************************development**************************/

/**
 * 异常捕获开启
 */
define('DEBUG',true);

/**
 * firphp 调试开启 需要firefox 安装firephp扩展
 */
define('FIREPHP',true);

/**
 * 是否开启日志记录
 */
define('LOG',true);

/**
 * 日志记录路径 注意该目录需要读写权限
 */
define('LOGDIR',FRAME_PATH.'/log/');


/**********************************production***************************/


?>
