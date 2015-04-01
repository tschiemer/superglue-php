<?php

namespace Superglue\Command;

class Mkdir implements \Superglue\Interfaces\Command {
    
    public static function run($argc, $argv){
        
        if ($argc != 1){
            throw new \Superglue\Exceptions\Exception('Wrong argument count',300);
        }
        list($path) = $argv;
        
        $path = \Superglue::path($path);
        
        mkdir($path,0777,TRUE);
    }
}