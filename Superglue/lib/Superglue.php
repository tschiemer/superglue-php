<?php

//namespace Superglue;
use Superglue\Exceptions\Exception as Exception;
use Superglue\Exceptions\NotFoundException as NotFoundException;

/**
 * 
 */
class Superglue {
    
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
        
        if ($this->request->method() == 'post' and $this->request->uri() == '/cmd'){
            
            $this->auth->authorize();
            
            return $this->serveCommand();
        }
        
//        var_dump('/\/'.preg_quote($this->config->callbackPrefix,'/').'(.+)/');
        if (preg_match('/^\/'.preg_quote($this->config->callbackPrefix,'/').'([a-zA-Z]{1,1}[a-zA-Z0-9]+)\/([a-zA-Z]+)((\/[a-zA-Z0-9-]+)*)/',$this->request->uri(),$matches)){
//            var_dump($matches);
            $controller = $matches[1];
            $method = $matches[2];
            if (empty($matches[3])){
                $params = array();
            } else {
                $params = explode('/',substr($matches[3],1));
            }
            
            if ($this->serveController($controller,$method,$params)){
                return;
            }
                    
        }
        
        if ($this->request->method() === 'post'){
            // assume attempting to upload file
            return $this->serveFileUpload();
        }
    }
    
    
    public function serveController($controller,$method,$params){
        
        $methodName = $this->request->method() . ucfirst($method);
        
        if (in_array($controller,array('auth'))){
            if (method_exists($this->$controller, $methodName)){
                call_user_func_array(array($this->$controller,$methodName), $params);
                exit;
            }
        } elseif (class_exists($controller)){
            $check = new ReflectionClass($controller);
            if ($check->implementsInterface('\Superglue\Interfaces\Controller')
                and $check->hasMethod($methodName)){
                call_user_func_array("{$controller}::{$methodName}",$params);
                exit;
            }
        }
        
        throw new NotFoundException("Controller/method not found: {$controller}/{$method}");
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
            throw new NotFoundException("{$cmdName}: bad command");
        }
    }
    
    public function serveFileUpload(){
        $destination = self::path(".{$this->request->uri()}");
        $uploadedFile = $this->request->content();
        
        if ($uploadedFile['error'] != 0){
            throw new \Superglue\Exception('An upload error occurred',300);
        }
        
        move_uploaded_file($uploadedFile['tmp_name'], $destination);
    }
    
    
    
    
    public static function url($path){
        return self::$instance->config->url . $path;
    }
    
    public static function callbackUrl($path){
        return self::$instance->config->url . self::$instance->config->callbackPrefix . $path;
    }
    
    
    public static function path($path){
        
        $out = self::$instance->config->publicPath . $path;
        
        $DS = preg_quote(DIRECTORY_SEPARATOR,'/');
        
        $replacements = array(
            "/{$DS}+/"          => DIRECTORY_SEPARATOR,
            "/{$DS}\.\.{$DS}/"  => DIRECTORY_SEPARATOR,
            "/{$DS}\.\.$/"      => '',
            "/{$DS}\.{$DS}/"    => DIRECTORY_SEPARATOR ,
            "/{$DS}\.$/"        => ''
        );
        
        return preg_replace(array_keys($replacements), array_values($replacements), $out);
    }
    
    
    public static function resourceUrl($path){
        return self::$instance->config->resourceUrl . $path;
    }
    
    public static function resourcePath($path){
        return  self::$instance->config->resourcePath . $path;
    }
    
    public static function loadResource($path,$vars=array()){
        $path = self::resourcePath($path);
        extract($vars);
        ob_start();
        include $path;
        return ob_get_clean();
    }
}
