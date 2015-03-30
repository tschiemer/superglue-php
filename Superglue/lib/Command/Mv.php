<?php

namespace Superglue\Command;
use Superglue\Server as SG;

class Mv implements \Superglue\Interfaces\Command {
    
    public static function run($argc, $argv){
        
        if ($argc != 2){
            SG::abort(300,'Wrong argument count');
        }
        list($from,$to) = $argv;
        
        $from = SG::path($from);
        $to = SG::path($to);
        
        rename($from,$to);
    }
}