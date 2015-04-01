<?php

namespace Superglue\Interfaces;

interface Request {
    
    /**
     * @return string
     */
    public function method();
    
    /**
     * @return string
     */
    public function uri();
    
    /**
     * @return mixed
     */
    public function content();
//    
//    /**
//     * 
//     * @param string $filename
//     * @return array
//     * @throws \Superglue\Exceptions\Exception
//     */
//    public function file($filename);
}