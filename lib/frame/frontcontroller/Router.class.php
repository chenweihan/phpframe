<?php
/**
 * 路由解析
 *
 * @decription 请求路由解析，调用命令执行命令 
 * @author Chen weihan <csq-3@163.com>
 * @copyright v1.0
 * @package frame
 * @version v1.0 
 *
 */

/**
 * Router 路由解析类
 * 
 * eg: http://127.0.0.1/
       http://127.0.0.1/index.php
       http://127.0.0.1/index.php?r=home/user/serach&name='jack' [get]
       http://127.0.0.1/index.php?r=home/usr/serach  info='{name:jack}'[post]
 */
class Router {
    
    /**
     * 请求
     */
    private $requestArr = array();  
    
    /**
     * 参数
     */
    private $paramArr = array();    

    /**
     * 构造函数 默认启动
     */
    public function __construct() {

        $this->getRequest();
        //访问权限
        if ($this->accessRBAC()) {
             $this->getParam();
             $this->dispatcher();
        }
        //var_dump($this->requestArr); 
        //var_dump($this->paramArr);       
    } 
    
    /**
     * 接受请求前处理 firefox没有该属性
     */
    /*
    private function readyAccept() {
       if ($_SERVER['HTTP_ACCEPT_CHARSET'] !== 'UTF-8' ) {
          throw new FrameException("the request chareset is not utf-8 ",400);
          exit();
       }     
    }
    */
	
    /**
     * 获取请求分发数据[apache 才支持$_SERVER['REQUEST_URI']]
     */
    private function getRequest() {		

       if ($_SERVER['REQUEST_METHOD'] == 'POST') {         
               
             if (isset($_SERVER['REQUEST_URI'])) {                 
                    $uri = $_SERVER['REQUEST_URI'];
             } else {
                  if (isset($_SERVER['argv'])) {
                     $uri = $_SERVER['PHP_SELF'] .'?'. $_SERVER['argv'][0];
                  }
                  else {
                     $uri = $_SERVER['PHP_SELF'] .'?'. $_SERVER['QUERY_STRING'];
                  }
              }
              
              if (false === strpos($uri,'?')) {                              
                     $this->requestArr = explode('/',DEFAULT_PATH);
              } else {
		     $uriArr = explode('?',$uri);
		     if ( count($uriArr) == 2 ) {      
		        $this->requestArr = explode('/',array_pop($uriArr));
                     } else {
                        throw new FrameException('the post  request format is error',400);exit();
                     }
              }
       
       } else if ($_SERVER['REQUEST_METHOD']== 'GET') {
	    
               if (isset($_GET['r'])) {       
		  $this->requestArr = explode('/',$_GET['r']);
	       } else {                  
                  $this->requestArr = explode('/',DEFAULT_PATH);
	       }
       }  
    }
    
    /**
     * 获取请求参数
     */
    private function getParam() {
        
       if ($_SERVER['REQUEST_METHOD'] == 'GET') {
              
              //常规删除请求参数
              foreach ($_GET as $key=>$value) {
                    if ($key != 'r') {
                      $this->paramArr[$key] = $value;
                    }
              }
              
       } else if ($_SERVER['REQUEST_METHOD']== 'POST') {
              
              $info = isset($_POST['info']) ? $_POST['info'] : '';
              if ($info=='') {
                 $this->paramArr = json_decode($info,true);
              }

       } else {
          
            throw new FrameException(" Don't support request type ",500);
            exit();
       }
       
    }
    
    /**
     *  系统访问权限控制 基于RBAC
     */    
    private function accessRBAC() {
       
       return true;    
    } 

    /**
     * 分发请求
     * 如果类使用了命名空间，则命名空间名即为目录名
     *
     * 类命名空间，类常规，函数常规，函数命名空间均可调用
     *
     */
    private function dispatcher() {
      $requestPath='';  //文件路径
      $appDir = '';     //文件目录，命名空间
      $fileName ='';    //文件名 
      $isFile =  false;
      $isClass = false; //是否是类，否则是单纯的函数
      
      for($i=0,$len = count($this->requestArr); $i < $len ; $i++) {         
         //判断目录
         //echo APP_PATH.'/'.$this->requestArr[$i];
         if(is_dir(APP_PATH.'/'.$this->requestArr[$i])) {
              $appDir = $this->requestArr[$i];
         } else {             
                //判断页面
		if(!$isFile) {

				if(is_file(APP_PATH.'/'.$appDir.'/'.$this->requestArr[$i].'.php')) {
					$requestPath = APP_PATH.'/'.$appDir.'/'.$this->requestArr[$i].'.php';
					$isFile = true;//页面存在
					$fileName = ucfirst(strtolower($this->requestArr[$i]));
					require_once $requestPath; 			   
					//判断类 使用命名空间
					if(class_exists($appDir.'\\'.$this->requestArr[$i])) {
					  $class = new ReflectionClass($appDir.'\\'.$this->requestArr[$i]); 
					  $isClass = true;
					//判断类 全局类
					} else if (class_exists($this->requestArr[$i])) {
					  $class = new ReflectionClass($this->requestArr[$i]); 
					  $isClass = true;
					}
				} else {
				    throw new FrameException("the file not exist ");
				}
			
		 } else {
			
				//判断函数 [是类方法，还是命名空间，还是全局]
				if($isClass) {
					   //类方法调用
					   if($class->hasMethod($this->requestArr[$i])) {						   
														 
							 $reflectionMethod = new ReflectionMethod($fileName,$this->requestArr[$i]);								 																 
							 foreach ($reflectionMethod->getParameters() as $p) {
								//如果参数不存在	
								if (!isset($this->paramArr[$p->getName()])) {
								       throw new FrameException('Parameter with "' . $p->getName() . '" is invalid.',400);
								//如果执行函数为构造函数                         
								} else if ($reflectionMethod->isConstructor()) {
								       $this->checkParameters($reflectionMethod);
								       $class->newInstanceArgs($this->paramArr);
								} else { 
								       $this->checkParameters($reflectionMethod);
								       $reflectionMethod->invokeArgs($class->newInstance(),$this->paramArr);
								}
								
							 }
					   } else {
                                             throw new FrameException('the method is not exist',404);
                                           }                

				} else {
				   //判断命名空间函数
				   //echo $appDir.'\\'.strtolower($this->requestArr[$i]);						        
				   if (function_exists($appDir.'\\'.strtolower($this->requestArr[$i]))) {
				      $function = new ReflectionFunction($appDir.'\\'.strtolower($this->requestArr[$i]));
				    //判断函数是否存在，全局函数
				    } else if (function_exists(strtolower($this->requestArr[$i]))) {
				      $function = new ReflectionFunction(strtolower($this->requestArr[$i]));
				    } else {
				      throw new FrameException ('the class or function is not exist ',404);
				    }						    
				    //判断参数
				    $this->checkParameters($function);
				    //执行函数               
				    $function->invokeArgs($this->paramArr);
				}
			
		 }          

           }

       }	
    }
    
    /**
     * 检查参数
     */
    private function checkParameters($func) {

           foreach ($func->getParameters() as $p) {
	        if (!isset($this->paramArr[$p->getName()])) {
		     throw new FrameException('Parameter with "' . $p->getName() . '" is invalid.',400);
                     exit();
	        }
	   }	

    }


	
}

?>
