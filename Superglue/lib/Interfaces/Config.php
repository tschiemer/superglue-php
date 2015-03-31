<?php

namespace Superglue\Interfaces;

abstract class Config {
    
    protected $config = null;
    
    public function __construct($config){
        $this->config = $config;
    }
    
    public function __isset($name){
        return isset($this->config[$name]);
    }
    
    public function __get($name){
        if (!isset($this->config[$name])){
            throw new \Superglue\Exception("Config value '{$name}' does not exist!");
        }
        return $this->config[$name];
    }
    
    public function __set($name,$value){
        $this->config[$name] = $value;
        return $this;
    }
    
    public function __call($name, $arguments) {
        if (empty($arguments)){
            return $this->__get($name);
        } else {
            return $this->__set($name,reset($arguments));
        }
    }
    
    
    abstract public function isSavable();
    abstract public function save();
    
}