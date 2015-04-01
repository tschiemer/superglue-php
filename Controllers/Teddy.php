<?php

class Teddy implements \Superglue\Interfaces\Controller {
    
    public static function getHi(){
        echo "Hi Teddy!" . implode(' ',  func_get_args());
    }
    
}