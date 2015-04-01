<?php

namespace Superglue\Command;

class Mv implements \Superglue\Interfaces\Command {
    
    public static function run($argc, $argv){
        
        if ($argc != 2){
            throw new \Superglue\Exceptions\Exception('Wrong argument count',300);
        }
        list($from,$to) = $argv;
        
        $from = \Superglue::path($from);
        $to = \Superglue::path($to);
        
        rename($from,$to);
    }
}