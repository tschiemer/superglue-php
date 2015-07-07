<?php

class Auth extends \Superglue\Interfaces\Controller {
    
    
    public function getLogin(){
        echo \Superglue::loadResource('auth.login.php');
    }
    
    public function postLogin(){
        if (\Superglue::auth()->isAuthorized()){
            die('already logged in');
            return;
        }
        
        if (isset($_POST['user']) and isset($_POST['pass'])){
            if (\Superglue::auth()->login($_POST['user'],$_POST['pass'])){
                die('successfully logged in');
            }
        }
        
        $this->getLogin();
    }
    
    public function getLogout(){
        \Superglue::auth()->logout();
        die('logged out');
    }
    
}