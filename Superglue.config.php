<?php

return array(
    'publicBase' => __DIR__ . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR,
    
    'urlBase' => 'http://localhost:8000/dev/superglue/public/',
    
//    'auth' => array(
//        'driver'    => '\\Superglue\\Auth\\Http',
//        'options'   => array(
////            'method'    => 'basic',
////            'realm'     => 'superglue',
//            'user'      => 'philip',
//            'pass'      => '123'
//        )
    
    'auth' => array(
        'driver'    => '\\Superglue\\Auth\\Session',
        'options'   => array(
            'user'      => 'philip',
            'pass'      => '123'
            )
    )
    
    
);