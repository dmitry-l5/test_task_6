<?php
define("DEFAULT_CONTROLLER", "employees");
define("DEFAULT_ACTION", "index");
require_once('heap/db_config.php');
spl_autoload_register(function($class_name){
    require_once("heap/".$class_name.".php");
});
$db = DBManager::init($db_config);
$request = $_SERVER['REQUEST_URI'];
$routeInfo = Route::route($request);
if(!file_exists($routeInfo->get()['path'])){
    throw new Exception("Controller file - ".$routeInfo->get()['path']." - does't exist");
}
require_once($routeInfo->get()['path']);
$controller_name = $routeInfo->get()['controller'];
$controller = new $controller_name;
$action_name = "action_".$routeInfo->get()['action'];
$controller_result = null;
if(method_exists($controller, $action_name)){
    $controller_result = $controller->$action_name($routeInfo->get()['args']);
}else{
    throw new Exception("Action does't exist  - ".$controller_name.'->'.$action_name.'()');
}

$data = array(
    'index_1'=>1234,
    'index_2'=>3456,
    'index_3'=>5678,
);
if( 
    !isset($controller_result['view_name'])||
    !file_exists("views/view_".$controller_result['view_name'].".php")
){
    $controller_result['view_name'] = '404';
}
Page::init();
if(isset($controller_result['data'])){
    Page::assign('data', $controller_result['data']);
}
Page::display($controller_result['view_name']);