<?php

namespace Superglue\Config;

class Php extends \Superglue\Interfaces\Config {
    
    /**
     * @var array
     */
    protected $config = null;
    
    private $path;
    
    public function __construct($path){
        $this->path = $path;
        
        $default = require_once __ROOT__ . 'Superglue/default-config.php';
        $override = require_once $path;
        
        $this->config = array_merge($default,$override);
    }
    
    public function isSavable(){
        return true;
    }
    
    public function save(){
        $data = '';
        $serializer = function($k,$v,&$sum)use($serializer){
//            $sum .=
        };
        
        array_walk($this->config, $serializer,$data);
        echo $data;
//        file_put_contents($path, $data);
    }
    
}