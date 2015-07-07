<?php

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

    public static function config(){
        return self::$instance->config;
    }
    
    
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
    

    
    public function init(){
        
        if (isset($this->config->useInternalExceptionHandler) and $this->config->useInternalExceptionHandler){
            set_exception_handler(array($this,'exceptionHandler'));
        }
        
        if (isset($this->config->useInternalAutoloader) and $this->config->useInternalAutoloader){
            spl_autoload_register(array($this,'autoloader'));
        }

        if (isset($this->config->useHelpers) and $this->config->useHelpers){
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'Helpers.php';
        }
        
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

    public function exceptionHandler(Exception $e){

        $data = array(
            'exception' => $e,
            'uri'       => $this->request()->uri()
        );

        if ($e instanceof \Superglue\Exceptions\NotAuthorizedException){
            http_response_code(401);
            echo self::loadResource('errors/generic.php',$data);
        } elseif ($e instanceof \Superglue\Exceptions\NotFoundException){
            http_response_code(404);
            echo self::loadResource('errors/404-not-found.php',$data);
        } else {
            http_response_code(500);
            echo self::loadResource('errors/generic.php',$data);
        }

        exit;
    }

    public function autoloader ($class) {
        $base_dir = __ROOT__  . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR;

        $file = $base_dir . $class . '.php';

        if (file_exists($file)){
            require $file;
        }
    }
    
    public function run(){
        
//        var_dump('Method: '.$this->request->method);
//        var_dump('URI: '.$this->request->uri);
        
        /*
         *  1st: Handle any incoming commands (can not be overridden)
         */
        if ($this->request->method() == 'post' and $this->request->uri() == '/cmd'){
            
            $this->auth->authorize();
            
            return $this->serveCommand();
        }
        
        /*
         *  2nd: See if there's any controller that matches the request
         */
        $controllerName = ucfirst($this->request->segment(0));
        if (class_exists($controllerName)){
            
            // get all segments starting from second
            $params = $this->request->segments(1);
            
            if (empty($params)){
                $method = 'Index';
            } else {
                $method = ucfirst(array_shift($params));
            }
            
            foreach(array($this->request->method().':method','any:method','catchAll') as $action){
                $methodName = str_replace(':method',$method,$action);
                
                $reflect = new ReflectionClass($controllerName);
                if ($reflect->isSubclassOf('\Superglue\Interfaces\Controller')
                        and $reflect->hasMethod($methodName)){

                    return $this->serveController($controllerName, $methodName, $params);
                }
            }
        }
        
        
        /*
         * 3rd: assume that posts are file uploads and try to save files
         */
        if ($this->request->method() === 'post'){
            // assume attempting to upload file
            return $this->serveFileUpload();
        }
        
        
        throw new NotFoundException($this->request->uri());
    }
    
    public function serveController($controllerName, $methodName, $params){
        $controller = new $controllerName();
        $controller->sg = $this;
        return call_user_func_array(array($controller,$methodName),$params);
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
    
    

    /*****************************************************/
    /** Helper functions  **/
    
    public static function url($path)
    {
        return self::$instance->config->url . $path;
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
        $SuperglueRealResourcePathNeverPassThisVariable = self::resourcePath($path);
        extract($vars);
        unset($vars);
        ob_start();
        include $SuperglueRealResourcePathNeverPassThisVariable;
        return ob_get_clean();
    }
    
}
