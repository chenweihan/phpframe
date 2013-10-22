<?php
/**
 * 日志记录相关动作类
 * 
 * 系统异常报错，操作记录，运行状态等记录日志
 * @author chenweihan <csq-3@163.com>
 * @copyright v1.0
 * @since v1.0
 * @version v1.0
 * @package frame
 */

/**
 * 日志记录类
 * 注意：日志记录不能使用exit结束，否则其他程序无法正常运行
 */
class Log {

   /**
    * 日志统一格式 时间 错误码 信息 文件 行数
    * @param {int} $code
    * @param {string} $msg
    * @param {string} $file
    * @param {int} $line
    * @return string 
    */
    static private function logFormat($code,$msg,$file,$line) {
         $time = date("Y-m-d H:i:s");
         $data = $time." ".$code." ".$msg." ".$file." ".$line."\r\n";
         return $data;
    }
    
    /**
     * 日志记录     
     * @param {int} $code
     * @param {string} $msg
     * @param {string} $file
     * @param {int} $line
     */
    static public function logRecord($code,$msg,$file,$line) {
         $log = self::logFormat($code,$msg,$file,$line);
         self::writeLogFile($log);
    }

    /**
     * 判断LOG文件是否存在 文件按照时间月来命名 
     * @param {string} $log
     */
    static private function writeLogFile($log){
        $filename = date("Y-m"); 
        //判断目录
        if(!file_exists(LOGDIR)) {
           mkdir(LOGDIR,0777);
        }
        //写入日志
        file_put_contents(LOGDIR.$filename.".txt",$log,FILE_APPEND);     
    }
}






































?>
