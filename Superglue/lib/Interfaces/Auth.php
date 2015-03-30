<?php

namespace Superglue\Interfaces;

Interface Auth {
    
    public function authorize();
    
    public function logout();
    
    public function isAuthorized();
    
    public function isGuest();
    
    public function can($permission);
    
    public function user();
}
