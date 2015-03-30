<?php

namespace Superglue\Traits;

trait Singleton {
 
    /**
     * @var self
     */
    private static $instance = NULL;
    
    public static function singleton($options = array()){
        if (self::$instance === NULL){
            self::$instance = new self($options);
        }
        return self::$instance;
    }
    
    public static function singletonIsSet(){
        return (self::$instance !== NULL);
    }
    
    public function setAsSingleton(){
        self::$instance = $this;
    }
    
    public static function __callStatic($name,$params){
        $instance = self::singleton();
        if (isset($instance->$name)){
            if (count($params) == 0){
                return $instance->$name;
            } else {
                $instance->$name = reset($params);
                return $instance;
            }
        } else {
            return call_user_method_array($name, self::singleton(), $params);
        }
    }
    
}