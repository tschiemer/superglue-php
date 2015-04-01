<?php

namespace Superglue;

class Request  implements \Superglue\Interfaces\Request {
    
    /**
     * post/get/patch/etc
     * @var string
     */
    protected $method;
    
    /**
     *
     * @var sring
     */
    protected $uri;
    
    /**
     *
     * @var string
     */
    public $contentType;
//    
//    /**
//     * Encoding or charset
//     * @var string
//     */
//    public $contentEncoding;
    
    public function __construct(){
        
        $this->method = strtolower($_SERVER['REQUEST_METHOD']);
        
        $basedir = dirname($_SERVER['SCRIPT_NAME']);
        $this->uri = substr($_SERVER['REQUEST_URI'], strlen($basedir)-1);
        
//        var_dump($this->uri);
//        var_dump($basedir);
//        var_dump($_SERVER['REQUEST_URI']);
        
        if ($this->method == 'post'){
            $ct = strtolower($_SERVER["CONTENT_TYPE"]);
            if (preg_match('/^(.+)(; ?([a-z]+)=(.+)?)$/',$ct,$m)){
                $this->contentType = $m[1];
                $this->{'content'.ucfirst(strtolower($m[3]))} = $m[4];
//                var_dump($this);
//                exit;
            } else {
                $this->contentType = $ct;
            }
            
//            if ($this->contentType == 'application/x-www-form-urlencoded'){
//                $request->content = $_POST;
//            } else if ($request->contentType = 'application/octet-stream'){
//            } else {
//                $request->content = file_get_contents('php://input');
//            }
        }
    }
    
    public function method(){
        return $this->method;
    }
    
    public function uri(){
        return $this->uri;
    }
    
    public function content(){
//        var_dump($this);
//        exit;
        switch($this->contentType){
            case 'application/x-www-form-urlencoded':
                return $_POST['data'];
                
            case 'multipart/form-data':
                if (!isset($_FILES) or !isset($_FILES['userimage'])){
                    throw new \Superglue\Exception('No valid file uploaded',300);
                }
                return $_FILES['userimage'];
                
            default:
                
                var_dump($this);
                var_dump($_FILES);
                
                $in = file_get_contents('php://input');
                var_dump($in);
        }
    }
}
