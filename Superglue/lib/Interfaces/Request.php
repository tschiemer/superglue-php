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
     * @return string
     */
    public function queryString();
    
    /**
     * @param int $i
     */
    public function segment($i);
    
    /**
     * 
     * @param int $from
     * @param int $to
     */
    public function segments($from=NULL,$to=NULL);


    /**
     * @return mixed
     */
    public function arg($var,$default=NULL);

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