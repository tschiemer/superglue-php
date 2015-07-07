<?php

namespace Superglue\Auth;

use Superglue\Exceptions\Exception;

class Http implements \Superglue\Interfaces\Auth {

    protected $method = 'basic';
    
    protected $realm = 'superglue';
    
    protected $user = NULL;
    
    public function __construct($options=array()){
        
        if (strpos(strtolower(php_sapi_name()),'cgi')){
            $authorization = isset($_GET['PHP_AUTH_DIGEST_RAW']) ? $_GET['PHP_AUTH_DIGEST_RAW'] : FALSE;
            if (!empty($authorization)){
                list($method,$credentials) = explode(' ',$authorization);
                if (strtolower($method) === 'basic'){
                    list($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW']) = explode(':',base64_decode($credentials));
                } else {
                    throw new Exception("Currently only Basic Authentication is supported when PHP is running as CGI module.");
                }
            }
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
                throw new Exception("HTTP Digest Authentication is currently not supported, sorry.");
                break;
        }
        
    }
    
    public function authorize(){
        if (! $this->isAuthorized()){
            header("WWW-Authenticate: Basic realm=\"{$this->realm}\"");
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
