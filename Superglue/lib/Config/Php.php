<?php

namespace Superglue\Config;

class Php extends \Superglue\Interfaces\Config {
    
    public function __construct($path){
        $this->config = require_once $path;
    }
    
    
}