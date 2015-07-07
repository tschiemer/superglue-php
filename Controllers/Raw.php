<?php

use Superglue\Exceptions\NotAuthorizedException as NotAuthorizedException;
use Superglue\Exceptions\NotFoundException as NotFoundException;

class Raw extends \Superglue\Interfaces\Controller {
    
    public function __construct() {
        if (!Superglue::auth()->isAuthorized()){
//            throw new NotAuthorizedException();
        }
    }
    
    public function catchAll(){
        
        $segments = $this->sg->request()->segments(1);
        $path = DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $segments);
        $realPath = Superglue::path($path);

        if ($this->sg->request()->arg('data',FALSE)) {
//            if ($path === DIRECTORY_SEPARATOR) {
//                http_response_code(400);
//                echo "You did not specify a file";
//            } else
            if (!file_exists($realPath)) {
                echo json_encode(array(
                    'message': "File does not exist!"
                ));
            } elseif (is_dir($realPath)){
//                http_response_code(400);
//                echo "Dummy it's a directory";
                echo json_encode(array(
                    'type' => 'directory',
                    'data' => dir
                ));
            } else {
                echo json_encode(array(
                    'type' => 'file',
                    'data' => file_get_contents($realPath)
                ));
            }

        } else {

            echo Superglue::loadResource('raw.editor.php', array(
                'path' => $path,
                'CodeMirror' => array(
                    'theme' => 'monokai'
                )
            ));
        }
    }
    
}