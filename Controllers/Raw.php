<?php

use Superglue as Superglue;
use Superglue\Exceptions\NotAuthorizedException as NotAuthorizedException;
use Superglue\Exceptions\NotFoundException as NotFoundException;

class Raw implements \Superglue\Interfaces\Controller {
    
    public function __construct() {
        if (!Superglue::auth()->isAuthorized()){
//            throw new NotAuthorizedException();
        }
    }
    
    public function catchAll(){
        
        $segments = Superglue::request()->segments(1);
        $path = DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $segments);
        $realPath = Superglue::path($path);
        
        if (!file_exists($realPath)){
            throw new NotFoundException("Invalid filename");
        }

        echo Superglue::loadResource('rawForm.php',array(
            'realPath' => $realPath
        ));
    }
    
}