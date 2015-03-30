<?php

namespace Superglue\Command;
use Superglue\Server as SG;

class Ls implements \Superglue\Interfaces\Command {
    
    public static function run($argc,$argv){
        if ($argc != 2){
            SG::abort(300,"Wrong argument count");
        }
        list($optionsLA,$path) = $argv;
        
        $safePath = SG::path($path);
        
        $files = scandir($safePath, SCANDIR_SORT_ASCENDING);
        
        $files = array_filter($files, function($fname){
           return !($fname == '.' or $fname == '..'); 
        });
        
        $files = array_map(function($fname)use($path){
            
            $fname = $path . DIRECTORY_SEPARATOR . $fname;
            
            $realfile = SG::path($fname);
            
            $type = filetype($realfile);
            $size = filesize($realfile);
            $owner = posix_getpwuid(fileowner($realfile));
            $group = posix_getgrgid(filegroup($realfile));
            $timestamp = date('Y-m-d H:i:s.000000000 P',filectime($realfile));
            $perms = substr(sprintf('%o', fileperms($realfile)),-3);
            
            return "{$fname}\t{$type}\t{$size}\t{$timestamp}\t{$owner['name']}\t{$group['name']}\t{$perms}";
        }, $files);
        
        return implode("\n",$files);
    }
    
}