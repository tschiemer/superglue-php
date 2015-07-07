<?php

return array(
    
    'useInternalExceptionHandler' => TRUE,
    'useInternalAutoloader' => TRUE,
    'useHelpers' => TRUE,
    
    'url' => ((isset($_SERVER['HTTPS']) and $_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . str_replace('//','/',dirname($_SERVER['SCRIPT_NAME']) . '/'),
    
    'publicPath' => __ROOT__,
    
    'resourcePath' => __SUPERGLUE__ . '/resources' . DIRECTORY_SEPARATOR,
    'resourceUrl'   => str_replace('//','/',dirname($_SERVER['SCRIPT_NAME']) . '/Superglue/resources/'),
    
    'auth' => array(
        'driver'    => '\\Superglue\\Auth\\Http',
        'options'   => array(
            'method'    => 'basic',
            'realm'     => 'superglue',
            'user'      => 'admin',
            'pass'      => 'iwantahug'
        )
    )
);