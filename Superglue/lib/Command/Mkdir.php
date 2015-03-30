<?php

namespace Superglue\Command;
use Superglue\Server as SG;

class Mkdir implements \Superglue\Interfaces\Command {
    
    public static function run($argc, $argv){
        
        if ($argc != 2){
            SG::abort(300,'Wrong argument count');
        }
        list($path) = $argv;
        
        $path = SG::path($path);
        
        mkdir($path,0777,TRUE);
    }
}