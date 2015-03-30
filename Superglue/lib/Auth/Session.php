<?php

namespace Superglue\Auth;
use Superglue\Server as SG;

class Session implements \Superglue\Interfaces\Auth {
    
    protected $user = NULL;
    
    public function __construct($options = array()){
        session_start();
        
        if (isset($_SESSION['sg-login-token']) and isset(SG::request()->uri) and "/{$_SESSION['sg-login-token']}" == SG::request()->uri){
//            var_dump($_SESSION['sg-login-token']);
            unset($_SESSION['sg-login-token']);
            if (isset($_REQUEST['user']) and isset($_REQUEST['pass'])){
                if ($_REQUEST['user'] ==  $options['user'] and $_REQUEST['pass'] == $options['pass']){
                    $_SESSION['user'] = $_REQUEST['user'];
                }
            }
        }
        if (isset($_SESSION['user'])){
            $this->user = $_SESSION['user'];
        }
    }
    
    public function authorize() {
        if ($this->user == NULL){
            $loginToken = 'sg-login-'.md5(time());
            $_SESSION['sg-login-token'] = $loginToken;
            echo SG::loadResource('login.php',array('loginToken'=>$loginToken));
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

