<?php
/*
 * 定义根路径
 */
 define('ROOT',dirname(__FILE__));
 //定义类库路径
 define('LIB_DIR',ROOT."/lib");
 //定义接口路径
 define('ACT_DIR',ROOT."/act");
 
 /*
  * 引入公共类库
  */
 require_once ROOT."/Common/Common.php";
 require_once ROOT."/Common/function.php";
 /*
  * 按需引入类
  */
  function autoload($classname){
	$classname = str_replace('\\','/',$classname);
    if(file_exists(ROOT."/lib/".$classname."/".$classname.".php")){
        require_once ROOT."/lib/".$classname."/".$classname.".php";
    }elseif(file_exists(ROOT."/act/".$classname.".Act.php")){
        require_once ROOT."/act/".$classname.".Act.php";  
    }elseif(file_exists(ROOT."/mod/".$classname.".php")){
        require_once ROOT."/mod/".$classname.".php";
    }
  }
  spl_autoload_register("autoload");
/*spl_autoload_register(function($classname){
    $classname = str_replace('\\','/',$classname);
    if(file_exists(ROOT."/".$classname.".php")){
        require_once ROOT."/".$classname.".php";
    }elseif(file_exists(ROOT."/".$classname.".Act.php")){
        require_once ROOT."/".$classname.".Act.php";  
    }elseif(file_exists(ROOT."/".$classname.".php")){
        require_once ROOT."/".$classname.".php";
    }
});*/
/*
 * 得到控制器和方法
 */
$control = isset($_GET['c'])?$_GET['c']:'Index';
$act = isset($_GET['a'])?$_GET['a']:'index';
//$control = "act\\".$control;
/*
 * 使用反射实例化控制器
 */
if(class_exists($control)){
    $class = new ReflectionClass($control);
        // 首先取出控制器的所有方法 并且只过滤出 public 方法
    /*$methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
	$methods = array_map(function ($val) { // 利用回调函数 将非 static 函数的名称返回给数组
        if (! $val->isStatic())
            return $val->name;
    }, $methods);*/
	$methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);
    $methods = array_map("getnostatic", $methods);
    /*
     * 此判断是为了使方法名称不区分大小写
     * 判断当前方法是否在控制器中存在
     */
    $if = false;
    for ($i = 0; $i < count($methods); $i ++) {
        if (strtolower($act) == strtolower($methods[$i])) {
            $act = $methods[$i];
            $if = true;
            break;
        }
    }
    if($if){
	$class->getMethod($act)->invoke($class->newInstance());
    }else{
        echo "FUNC NOT FOUND";
    }
}
function getnostatic($val){
	if(!$val->isStatic()){
		return $val->name;
	}
}
