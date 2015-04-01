<?php

namespace Superglue\Command;

class Ls implements \Superglue\Interfaces\Command {
    
    public static function run($argc,$argv){
        if ($argc != 2){
            throw new \Superglue\Exception('Wrong argument count', 300);
        }
        list($optionsLA,$path) = $argv;
        
        $safePath = \Superglue::path($path);
        
        if (!file_exists($safePath)){
            return;
        }
        
        $files = scandir($safePath, SCANDIR_SORT_ASCENDING);
        
        $files = array_filter($files, function($fname){
           return !($fname == '.' or $fname == '..'); 
        });
        
        $path = str_replace('//','/',$path.DIRECTORY_SEPARATOR);
        
        $files = array_map(function($fname)use($path){
            
            $fname = $path . $fname;
            
            $realfile = \Superglue::path($fname);
            
            switch(filetype($realfile)){
                case 'dir':
                    $type = 'directory';
                    break;
                case 'file':
                    $type = 'regular file';
                    break;
                default:
                    $type = 'unknown';
            }
            $size = filesize($realfile);
            $owner = posix_getpwuid(fileowner($realfile));
            $group = posix_getgrgid(filegroup($realfile));
            $timestamp = date('Y-m-d H:i:s.000000000 P',filectime($realfile));
            $perms = substr(sprintf('%o', fileperms($realfile)),-3);
            
            return "{$fname}\t{$type}\t{$size}\t{$timestamp}\t{$owner['name']}\t{$group['name']}\t{$perms}";
        }, $files);
        
        return implode("\n",$files)."\n";
    }
    
}