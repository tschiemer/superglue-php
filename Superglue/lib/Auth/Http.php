<?php

namespace Superglue\Auth;

class Http implements \Superglue\Interfaces\Auth {
    
    protected $method = 'basic';
    
    protected $realm = 'superglue';
    
    protected $user = NULL;
    
    public function __construct($options=array()){
        
        if (strpos(strtolower(php_sapi_name()),'cgi')){
            throw new \Superglue\Exception("Can NOT use HTTP authentication when PHP is running as CGI module.");
        }
        
        if (isset($options['method'])){
            $this->method = $options['method'];
        }
        
        if (isset($options['realm'])){
            $this->method = $options['realm'];
        }
        
        switch($this->method){
            case 'basic':
                if (isset($_SERVER['PHP_AUTH_USER']) and isset($_SERVER['PHP_AUTH_PW'])){
                    if ($_SERVER['PHP_AUTH_USER'] == $options['user'] and $_SERVER['PHP_AUTH_PW'] == $options['pass']){
                        $this->user = new \stdClass();
                        $this->user->username = $_SERVER['PHP_AUTH_USER'];
                    }
                }
                break;
                
            case 'digest':
                break;
        }
        
    }
    
    public function authorize(){
        if (! $this->isAuthorized()){
            header('WWW-Authenticate: Basic realm="My Realm"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'Text, der gesendet wird, falls der Benutzer auf Abbrechen drückt';
            exit;
        }
    }
    
    public function logout(){
        
    }
    
    public function isAuthorized(){
        return $this->user != NULL;
    }
    
    public function isGuest(){
        return $this->user == NULL;
    }
    
    public function can($permission){
        
    }
    
    public function user(){
        return $this->user;
    }
}
