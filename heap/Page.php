<?php

class Page{
    static $_data = null;
    static private $instance = null;
    private function __construct(){}
    static public function init(){
        if(!self::$instance){
            self::$_data = array();
            self::$instance = new self;
        }
        return self::$instance;
    }

    public static function assign($index, $value){
        static::$_data[$index]=$value;
    }
    public static function display($view_name){
        include("views/view_".$view_name.".php");
    }
    private  static function getData($index, $subindex = null){
        if($subindex){
            if(isset(static::$_data[$index])&&
            isset(static::$_data[$index][$subindex])){
                return static::$_data[$index][$subindex];
            }else{
                return null;
            }
        }
        if(isset(static::$_data[$index])){
            return static::$_data[$index];
        }else{
            return null;
        }
    }

}
