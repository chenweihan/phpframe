<?php
/**
 * 框架级异常类,继承类EXCEPTION
 * 
 * @decription frame错误异常得捕获
 * @author Chen weihan <csq-3@163.com>
 * @version v1.0
 * @copyright v1.0
 * package frame
 */


/**
 * FrameException 框架异常类
 * 主要处理框架异常,以及header头部错误信息[401,402,403,404....]输出
 * 非header错误，从1000开始
 * @example throw new FrameException($message,$code,$file,$line)
 * $message is json format
 * eg:throw new FrameException('this file',404);
 *    throe new FrameException('this file',1000);
 * 注意：抛出http错误需要使用firebug等调试工具方能查看请求是否正确。
 */
class FrameException extends Exception {
   
    /**
     * 505 代码分界线，505以内为header输出
     */
    private $httpCode = 505;
   
    /**
     * 错误异常信息
     */
    private $_errorCode;
    
    /**
     * 错误异常码
     */
    private $_errorMsg; 
    
    /**
     * 错误异常文件
     */
    private $_errorFile;

    /**
     * 错误异常行
     */
    private $_errorLine; 
    
    /**
     * 错误码错误定义
     * @param {int} 错误 码
     * @return {string} 错误码字符串
     */
    static private function httpResponseCode($code) {
	  
           switch ($code) {
		    case 100: $text = 'Continue'; break;
		    case 101: $text = 'Switching Protocols'; break;
		    case 200: $text = 'OK'; break;
		    case 201: $text = 'Created'; break;
		    case 202: $text = 'Accepted'; break;
		    case 203: $text = 'Non-Authoritative Information'; break;
		    case 204: $text = 'No Content'; break;
		    case 205: $text = 'Reset Content'; break;
		    case 206: $text = 'Partial Content'; break;
		    case 300: $text = 'Multiple Choices'; break;
		    case 301: $text = 'Moved Permanently'; break;
		    case 302: $text = 'Moved Temporarily'; break;
		    case 303: $text = 'See Other'; break;
		    case 304: $text = 'Not Modified'; break;
		    case 305: $text = 'Use Proxy'; break;
		    case 400: $text = 'Bad Request'; break;
		    case 401: $text = 'Unauthorized'; break;
		    case 402: $text = 'Payment Required'; break;
		    case 403: $text = 'Forbidden'; break;
		    case 404: $text = 'Not Found'; break;
		    case 405: $text = 'Method Not Allowed'; break;
		    case 406: $text = 'Not Acceptable'; break;
		    case 407: $text = 'Proxy Authentication Required'; break;
		    case 408: $text = 'Request Time-out'; break;
		    case 409: $text = 'Conflict'; break;
		    case 410: $text = 'Gone'; break;
		    case 411: $text = 'Length Required'; break;
		    case 412: $text = 'Precondition Failed'; break;
		    case 413: $text = 'Request Entity Too Large'; break;
		    case 414: $text = 'Request-URI Too Large'; break;
		    case 415: $text = 'Unsupported Media Type'; break;
		    case 500: $text = 'Internal Server Error'; break;
		    case 501: $text = 'Not Implemented'; break;
		    case 502: $text = 'Bad Gateway'; break;
		    case 503: $text = 'Service Unavailable'; break;
		    case 504: $text = 'Gateway Time-out'; break;
		    case 505: $text = 'HTTP Version not supported'; break;
		    default: exit('Unknown http status code "' . htmlentities($code) . '"'); break;
              }

              return $text;
    }
    
    /**
     * 头部信息输出
     * @return {array} 异常信息错误数组
     */
    private function outputHeader() {
   
       $text = $this->_errorMsg.' '.self::httpResponseCode($this->_errorCode); 
       header("HTTP/1.0 ".$this->_errorCode." ".$text);
       
       $arrMsg = array(
           'code' => $this->_errorCode,
           'message' =>$text,
           'file' => $this->_errorFile,
           'line' => $this->_errorLine
       );
       //var_dump($arrMsg);
       //exit();
       return $arrMsg;
    }            
    
    /**
     * 代码级错误异常获取
     * @return {array} 异常错误信息数组
     */
    private function outputCode() {
       
       $arrMsg = array(
           'code' => $this->_errorCode,
           'message' => $this->_errorMsg,
           'file' => $this->_errorFile,
           'line' => $this->_errorLine
       );

       return $arrMsg;
    
     }

    /**
     *  输出异常信息
     * 
     *  根据返回得代码码来判断输出类型
     */
    public function errorMsg() {
     
       $this->_errorCode = $this->getCode();
       $this->_errorMsg =  $this->getMessage();
       $this->_errorFile = $this->getFile();
       $this->_errorLine = $this->getLine();	       
       // echo $this->_errorCode;
       
       /**
        * 日志记录
        */   
       if(LOG) {
               Log::logRecord($this->_errorCode,$this->_errorMsg,$this->_errorFile,$this->_errorLine); 
       }
       
       //需要开关控制是否启框架异常获取 
       if (DEBUG) {		 
	       // 抛出异常格式不正确
	       if($this->_errorCode ==  "" || $this->_errorCode == null || $this->_errorMsg == "" || $this->_errorMsg == null) {
		  
		  $this->_errorCode = $this->httpCode;
		  $this->_errorMsg = 'exception format is not correct';
		  $this->outputHeader();

	       }
	       //框架代码输出[框架内memcached,session等异常非http错误] 
	       else if ($this->_errorCode > $this->httpCode) {
                  
		   return $this->outputCode();
	       
	       }
	       //框架系统输出[http异常错误]
	       else {
	      
                   return $this->outputHeader(); 
	       
	       }
        } 
   }
}

?>
