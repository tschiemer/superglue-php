<?php

namespace Superglue\Command;
use Superglue\Server as SG;

class Rm implements \Superglue\Interfaces\Command {
    
    public static function run($argc, $argv){
        
        if ($argc != 1){
            SG::abort(300,'Wrong argument count');
        }
        list($path) = $argv;
        
        $path = SG::path($path);
        
        unlink($path);
    }
}