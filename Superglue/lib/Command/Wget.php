<?php

namespace Superglue\Command;

//
//wget http://localhost/resources/empty.html -O ./NewFolder-10/ffffNewPage.html
//
class Wget implements \Superglue\Interfaces\Command {
    
    public static function run($argc, $argv){
        
        if ($argc != 3){
            throw new \Superglue\Exceptions\Exception('Wrong argument count',300);
        }
        list($src,$option,$out) = $argv;
        
        $out = \Superglue::path($out);
        
        if ($src == 'http://localhost/resources/empty.html'){
            $src = 'http://localhost/resources/empty.php';
        }
        
        if (preg_match('/^'.  preg_quote('http://localhost/resources/', '/').'(.+\.([^.]+))/', $src, $matches)){
//            var_dump($matches);
            if (in_array($matches[2],array('php'))){
                $src = \Superglue::loadResource($matches[1]);
//                var_dump($src);
                file_put_contents($out, $src);
            } else {
                $src = \Superglue::resourcePath($matches[1]);
                copy($src,$out);
            }
        } else {
            
        }
    }
}

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

