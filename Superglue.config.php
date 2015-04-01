<?php

return array(
    
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