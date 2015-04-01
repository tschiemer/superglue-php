<?php

namespace Superglue\Command;

class Rm implements \Superglue\Interfaces\Command {
    
    public static function run($argc, $argv){
        
//        if ($argc = 2){
//            throw new \Superglue\Exception('Wrong argument count',300);
//        }
        if ($argc == 1){
            list($path) = $argv;
        } else if ($argc == 2){
            list($optionRecursive,$path) = $argv;
        } else {
            throw new \Superglue\Exceptions\Exception('Wrong argument count', 300);
        }
        
        $path = \Superglue::path($path);
        
        self::delete($path);
    }
    
    /**
     * 
     * @param string $dirPath
     * @throws InvalidArgumentException
     * @author http://stackoverflow.com/questions/3349753/delete-directory-with-files-in-it
     */
    private static function delete($path){
        if (is_file($path)){
            unlink($path);
            return;
        } else if (is_dir($path)){
            if (substr($dirPath, - 1) != '/') {
                $dirPath .= '/';
            }
            $files = glob($path . '*', GLOB_MARK);
            foreach ($files as $file) {
                if (is_dir($file)) {
                    self::delete($file);
                } else {
                    unlink($file);
                }
            }
            rmdir($path);
        }
    }
}