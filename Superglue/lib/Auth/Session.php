<?php

namespace Superglue\Auth;

class Session implements \Superglue\Interfaces\Auth {
    
    private $options = NULL;
    
    protected $user = NULL;
    
    public function __construct($options = array()){
        session_start();
        
        $this->options = $options;
        
        if (isset($_SESSION['user'])){
            $this->user = $_SESSION['user'];
        }
    }
    
    
    
    public function authorize() {
        if ($this->user == NULL){
            
            header('HTTP/1.0 401 Unauthorized');
            header('Location: '.\Superglue::callbackUrl('auth/login'));
            exit;
        }
    }
    
    public function login($user,$pass){
        if ($user ==  $this->options['user'] and $pass == $this->options['pass']){
            $_SESSION['user'] = $user;
            return TRUE;
        }
        return FALSE;
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
    
    
    
//    public function getLogin(){
//        echo \Superglue::loadResource('login.php');
//    }
//    
//    public function postLogin(){
//        if ($this->isAuthorized()){
//            die('already logged in');
//            return;
//        }
//        
//        
//        if (isset($_REQUEST['user']) and isset($_REQUEST['pass'])){
//            if ($_REQUEST['user'] ==  $this->options['user']
//                    and $_REQUEST['pass'] == $this->options['pass']){
//                $_SESSION['user'] = $_REQUEST['user'];
//                die('success!');
//            }
//        }
//        
//        $this->getLogin();
//        
//    }
//    
//    public function getLogout(){
//        $this->logout();
//        die('logged out');
//    }
    
    
}

