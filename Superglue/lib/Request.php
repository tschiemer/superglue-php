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
     * @var string
     */
    protected $queryString;
    
    /**
     *
     * @var array
     */
    protected $segments;
    
    /**
     *
     * @var string
     */
    protected $contentType;
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

        $queryPos = strpos($this->uri,'?');
        if (is_int($queryPos)){
            $this->queryString = substr($this->uri,$queryPos+1);
            $this->uri = substr($this->uri,0,$queryPos);
        } else {
            $this->queryString = FALSE;
        }
        
        $this->segments = explode('/',substr($this->uri,1));
        
        
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

    public function queryString(){
        return $this->queryString;
    }
    
    public function segment($i){
        return $this->segments[$i];
    }
    
    public function segments($from = 0, $to = NULL){
        if ($to === NULL){
            $to = count($this->segments);
        }
        return array_slice($this->segments, $from, $to - $from + 1);
    }

    public function arg($var,$default=NULL){
        if (array_key_exists($var,$_GET)){
            return $_GET[$var];
        }
        if (array_key_exists($var,$_POST)){
            return $_POST[$var];
        }
        return $default;
    }
    
    public function content(){
        switch($this->contentType){
            case 'application/x-www-form-urlencoded':
                return $_POST['data'];
                
            case 'multipart/form-data':
                if (!isset($_FILES) or !isset($_FILES['userimage'])){
                    throw new \Superglue\Exception('No valid file uploaded',300);
                }
                return $_FILES['userimage'];
                
            default:
                return file_get_contents('php://input');
                
        }
    }

    
//    public function file($filename){
//        
//    }
}
