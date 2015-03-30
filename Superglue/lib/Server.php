<?php

namespace Superglue;
use \Superglue\Superglue as SG;


/**
 * 
 */
class Server {
    
    private static $instance;
    
    /**
     *
     * @var Request
     */
    protected $request;
    
    public static function request(){
        return Server::$instance->request;
    }
    
    /**
     *
     * @var \Superglue\Config\Base
     */
    protected $config;
    
    public static function config(){
        return Server::$instance->config;
    }
    
    
    protected $auth;
    
    public static function auth(){
        return Server::$instance->auth;
    }
    
    
    public function __construct(\Superglue\Interfaces\Config $config) {
        Server::$instance = $this;
        
        $this->config = $config;
        
        /**
         * Get REQUEST parameters
         */
        $this->request = $request = new \stdClass();
        
        $request->method = strtolower($_SERVER['REQUEST_METHOD']);
        
        $basedir = dirname($_SERVER['SCRIPT_NAME']);
        $request->uri = substr($_SERVER['REQUEST_URI'], strlen($basedir));
        
        if ($request->method == 'post'){
            $ct = strtolower($_SERVER["CONTENT_TYPE"]);
            if (preg_match('/^(.+); charset=(.+)$/',$ct,$m)){
                $request->contentType = $m[1];
                $request->contentCharset = $m[2];
            } else {
                $request->contentType = $ct;
            }
            
            if ($request->contentType == 'application/x-www-form-urlencoded'){
                $request->content = $_POST;
            } else if ($request->contentType = 'application/octet-stream'){
            } else {
                $request->content = file_get_contents('php://input');
            }
        }
        
//        var_dump($request->contentType);
        
        
        /**
         * Load Auth Module
         */
        $authClassname = $config->auth['driver'];
        $this->auth = new $authClassname($config->auth['options']);
    }
    
    public function run(){
        
//        $this->abort();
        
//        var_dump('Method: '.$this->request->method);
//        var_dump('URI: '.$this->request->uri);
        
        if ($this->request->method === 'get'){
            
            if (preg_match('/(extension|resources)\/(.+)/',$this->request->uri,$matches)){
                $file = "Superglue/{$matches[0]}";
                
                if (!file_exists($file)){
                    self::abort(404,"File not found: {$this->request->uri}");
                }
                
                $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
                $mime = finfo_file($finfo, $file);
                finfo_close($finfo);
                
                header("Content-Type: {$mime}");
                readfile($file);
                exit;
            }
            
        } else if ($this->request->method === 'post'){
            
            $this->auth->authorize();
            
            if($this->request->uri == '/cmd'){
                $varg = explode(' ',$this->request->content);
                $cmdName = reset($varg);

                $cmdClassName = '\\Superglue\\Command\\'.ucfirst(strtolower($cmdName));
                if (class_exists($cmdClassName, TRUE)){
                    array_shift($varg);
                    $out = call_user_func("{$cmdClassName}::run",count($varg),$varg);

                    header('Content-Type: text/plain');
                    echo $out;
                } else {
                    /*
                     * errorCheck '1' "'$_EXE': bad command" ;;
                     */
                    $this->abort(404, "{$cmdName}: bad command");
                }
            } else {
                // assume attempting to upload file
                $fname = ".{$this->request->uri}";
                file_put_contents($fname, $this->request->content);
            }
        }
               
//        var_dump($this);
    }
    
    public static function abort($code=300,$message=NULL){
//        switch($code){
//            default:
                throw new Exception($message,$code);
//        }
    }

    public static function url($path){
        return self::config()->urlBase.$path;
    }
    
    public static function path($path){
        
        $out = self::config()->publicBase . $path;
        
        $DS = preg_quote(DIRECTORY_SEPARATOR,'/');
        
        $replacements = array(
            "/{$DS}+/"        => DIRECTORY_SEPARATOR,
            "/{$DS}\.\.({$DS}?)/" => '$1',
            "/{$DS}\.({$DS}?)/"  => '$1'
        );
        
        return preg_replace(array_keys($replacements), array_values($replacements), $out);
    }
    
    
    public static function resourcePath($path){
        if (isset(self::config()->resourceBase)){
            $resourceBase = self::config()->resourceBase;
        } else {
            $resourceBase = realpath(__DIR__.'/../resources').DIRECTORY_SEPARATOR;
        }
        return  $resourceBase . $path;
    }
    
    public static function loadResource($path,$vars=array()){
        $path = self::resourcePath($path);
        extract($vars);
//        var_dump($path);
        ob_start();
        include $path;
        return ob_get_clean();
    }
}
