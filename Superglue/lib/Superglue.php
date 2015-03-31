<?php

//namespace Superglue;


/**
 * 
 */
class Superglue {
    
//    const SUPERGLUE_CALLBACK = 'Superglue://';
    
    /**
     *
     * @var \Superglue
     */
    protected static $instance = NULL;
    
    public function __construct(\Superglue\Interfaces\Config $config) {
        $this->config = $config;
        
        self::$instance = $this;
    }
    
    /**
     *
     * @var \Superglue\Config\Base
     */
    protected $config;
    
//    public function config(){
//        return $this->config;
//    }
    
    /**
     *
     * @var \Superglue\Request
     */
    protected $request = NULL;
    
    public static function request(\Superglue\Interfaces\Request $request = NULL){
        if ($request != NULL){
            self::$instance->request = $request;
        }
        return self::$instance->request;
    }
    
    /**
     *
     * @var \Superglue\Interfaces\Auth
     */
    protected $auth;
    
    public static function auth(\Superglue\Interfaces\Auth $auth = NULL){
        if ($auth != NULL){
            self::$instance->auth = $auth;
        }
        return self::$instance->auth;
    }
    
//    
//    /**
//     *
//     * @var \Superglue\Interfaces\FileSystem
//     */
//    protected $fs;
//    
//    public function fs(\Superglue\Interfaces\FileSystem $fs = NULL){
//        if ($fs != NULL){
//            $this->fs = $fs;
//            return $this;
//        }
//        return $this->fs;
//    }
    
    
//    
//    public static function __callStatic($name, $arguments) {
//        if (in_array($name,array('request','config','auth','fs'))){
//            return call_user_method_array($name, self::$instance, $arguments);
//        }
//    }
    
    
    public function init(){
        
        /**
         * Load Default request module
         */
        if ($this->request == NULL){
            if (isset($config->request)){
                $requestClassname = $config->request['driver'];
                $options = isset($config->request['options']) ? $config->request['options'] : array();
                $this->request = new $requestClassname($options);
            } else {
                $this->request = new \Superglue\Request();
            }
        }
        
        /**
         * Load Auth Module
         */
        if ($this->auth == NULL){
            $authClassname = $this->config->auth['driver'];
            $options = isset($this->config->auth['options']) ? $this->config->auth['options'] : array();
            $this->auth = new $authClassname($options);
        }
        
        
        assert($this->request != NULL, "Request is set.");
        assert($this->auth != NULL, "Authorization is set.");
        
        return $this;
    }
    
    public function run(){
        
//        $method = $this->request->method();
//        $uri = $this->request->uri();
        
//        $this->abort();
        
//        var_dump('Method: '.$this->request->method);
//        var_dump('URI: '.$this->request->uri);
        
//        processFlashRoutes();
        
//        var_dump('/\/'.preg_quote($this->config->callbackPrefix,'/').'(.+)/');
        if (preg_match('/^\/'.preg_quote($this->config->callbackPrefix,'/').'([a-zA-Z]+)\/([a-zA-Z]+)((\/[a-zA-Z0-9-]+)*)/',$this->request->uri(),$matches)){
//            var_dump($matches);
            $controller = $matches[1];
            $method = $matches[2];
            if (empty($matches[3])){
                $params = array();
            } else {
                $params = explode('/',substr($matches[3],1));
            }
            $this->serveController($controller,$method,$params);
            exit;
        }
        
        if ($this->request->method() === 'post'){
            
            $this->auth->authorize();
            
//            var_dump($this->request->uri());
            
            if($this->request->uri() == '/cmd'){
                $this->serveCommand();
            } else {
                // assume attempting to upload file
                $this->serveFileUpload();
            }
            
        }
//        else if ($this->request->method() === 'get'){
//            
//            if (preg_match('/(extension|resources)\/(.+)/',$this->request->uri(),$matches)){
//                $file = "Superglue/{$matches[0]}";
//                
//                if (!file_exists($file)){
//                    self::abort(404,"File not found: {$this->request->uri()}");
//                }
//                
//                $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
//                $mime = finfo_file($finfo, $file);
//                finfo_close($finfo);
//                
//                header("Content-Type: {$mime}");
//                readfile($file);
//                exit;
//            }
//        }
               
//        var_dump($this);
    }
    
//    public static function flashRoute($route,$handler){
//        session_start();
//        if (!isset($_SESSION['sg-routes'])){
//            $_SESSION['sg-routes'] = array();
//        }
//        $_SESSION['sg-routes'][$route] = $handler;
//    }
//    
//    public function processFlashRoutes(){
//        session_start();
//        if (isset($_SESSION['sg-routes'])){
//            $uri = $this->request()->uri();
//            $routes = array_filter(array_keys($_SESSION['sg-routes']),function($key)use($uri){
//                return $key == $uri;
//            });
//            var_dump($routes);
//        }
//    }
    
    public function serveController($controller,$method,$params){
        
        $methodName = $this->request->method() . ucfirst($method);
        
        if (in_array($controller,array('auth'))){
            if (method_exists($this->$controller, $methodName)){
                call_user_func_array(array($this->$controller,$methodName), $params);
            }
        } elseif (class_exists($controller)){
            $check = new ReflectionClass($controller);
            if ($check->implementsInterface('\Superglue\Interfaces\Controller')
                and $check->hasMethod($methodName)){
                call_user_func_array("{$controller}::{$methodName}",$params);
            }
        }
        
        throw new \Superglue\Exception("Controller/method not found: {$controller}/{$method}",404);
    }
    
    public function serveCommand(){
        $varg = explode(' ',$this->request->content());
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
            throw new \Superglue\Exception("{$cmdName}: bad command",404);
        }
    }
    
    public function serveFileUpload(){
        $fname = ".{$this->request->uri()}";
//        file_put_contents($fname, $this->request->content);
    }
//    
//    public static function abort($code=300,$message=NULL){
////        switch($code){
////            default:
//                throw new Exception($message,$code);
////        }
//    }

    
    public static function url($path){
        return self::$instance->config->url . $path;
    }
    
    public static function callbackUrl($path){
        return self::$instance->config->url . self::$instance->config->callbackPrefix . $path;
    }
    
    public static function resourceUrl($path){
        return self::$instance->config->resourceUrl . $path;
    }
    
    public static function path($path){
        
        $out = self::$instance->config->publicBase . $path;
        
        $DS = preg_quote(DIRECTORY_SEPARATOR,'/');
        
        $replacements = array(
            "/{$DS}+/"          => DIRECTORY_SEPARATOR,
            "/{$DS}\.\.{$DS}/"  => DIRECTORY_SEPARATOR,
            "/{$DS}\.\.$/"  => '',
            "/{$DS}\.{$DS}/"     => DIRECTORY_SEPARATOR ,
            "/{$DS}\.$/"        => ''
        );
        
        return preg_replace(array_keys($replacements), array_values($replacements), $out);
    }
    
    
    public static function resourcePath($path){
        if (isset(self::$instance->config->resourceBase)){
            $resourceBase = self::$instance->config->resourceBase;
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
