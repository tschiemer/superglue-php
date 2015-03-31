<?php

namespace Superglue\Command;
use Superglue\Server as SG;

class Cp implements \Superglue\Interfaces\Command {
    
    public static function run($argc, $argv){
        
        if ($argc != 2){
            throw new Exception('Wrong argument count', 300);
        }
        list($from,$to) = $argv;

        $from = SG::path($from);
        $to = SG::path($to);
        
        copy($from,$to);
    }
}