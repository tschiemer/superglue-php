<?php

class Teddy extends \Superglue\Interfaces\Controller {
    
    public function getIndex(){
        echo "This is for real, dude!";
    }
    
    public function getHi(){
        echo "Hi Teddy!" . implode(' ',  func_get_args());
    }
    
}