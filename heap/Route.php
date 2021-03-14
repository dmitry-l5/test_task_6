<?php
class Route{

    static private $instance = null;
    private function __construct(){}
    static public function init(){
        if(!self::$instance){
            self::$instance = new self;
        }
        return self::$instance;
    }
    public static function route($path){
        $default_controller = defined('DEFAULT_CONTROLLER')?DEFAULT_CONTROLLER:"index";
        $default_action = defined('DEFAULT_ACTION')?DEFAULT_ACTION:"index";
        $url = parse_url($path);
        preg_match("~/([^/]*)/?([^/]*)~", $url["path"], $match);
        if(empty($match[2])){
            $controller_name = $default_controller;
            $action_name = empty($match[1])?$default_action:$match[1];
        }else{
            $controller_name = empty($match[1])?$default_controller:$match[1];
            $action_name = empty($match[2])?$default_action:$match[2];
        }
        isset($url["query"]) ?
            parse_str($url["query"], $args_arr):
            $args_arr = null;
        return new RouteInfo($controller_name, $action_name, $args_arr);
        return;
    }
}

class RouteInfo{
    private $controller;
    private $action;
    private $args;
    public function __construct($controller, $action, $args, $path = null){
        $this->controller = $controller;
        $this->action = $action;
        $this->args = $args;
        $this->path = $path?$path:"controllers/controller_".$controller.".php";
    }
    public function get(){
        return $this->get_arr();
    }
    public function get_arr(){
        return array(
            "path"=>$this->path,
            "controller"=>$this->controller,
            "action"=>$this->action,
            "args"=>$this->args,
        );
    }
}