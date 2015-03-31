<?php

namespace Superglue\Auth;

class Session implements \Superglue\Interfaces\Auth, \Superglue\Interfaces\Controller {
    
    private $options = NULL;
    
    protected $user = NULL;
    
    public function __construct($options = array()){
        session_start();
        
        $this->options = $options;
        
        if (isset($_SESSION['user'])){
            $this->user = $_SESSION['user'];
        }
    }
    
    public function postLogin(){
        if ($this->isAuthorized()){
            return;
        }
        
        
        if (isset($_REQUEST['user']) and isset($_REQUEST['pass'])){
            if ($_REQUEST['user'] ==  $this->options['user']
                    and $_REQUEST['pass'] == $this->options['pass']){
                $_SESSION['user'] = $_REQUEST['user'];
                die('success!');
            }
        }
        
        die('failure!');
    }
    
    public function authorize() {
        if ($this->user == NULL){
//            $loginToken = 'sg-login-'.md5(time());
//            \Superglue::flashRoute($loginToken, '')
//            $_SESSION['sg-login-token'] = $loginToken;
            
            header('HTTP/1.0 401 Unauthorized');
            echo \Superglue::loadResource('login.php');
            exit;
        }
    }
    public function logout() {
        unset($_SESSION['user']);
        $this->user = NULL;
    }

    public function isAuthorized() {
        return $this->user != NULL;
    }

    public function isGuest() {
        return $this->user == NULL;
    }

    public function can($permission) {
        
    }
    
    public function user(){
        return $this->user;
    }
}

