<?php

return array(
    'url' => getenv('SUPERGLUE_BASE') ? getenv('SUPERGLUE_BASE') : str_replace('//','/',dirname($_SERVER['SCRIPT_NAME']) . '/'),
    
    'callbackPrefix' => getenv('SUPERGLUE_CALLBACK') ? getenv('SUPERGLUE_CALLBACK') : '',
    
    'publicPath' => __ROOT__ . 'public' . DIRECTORY_SEPARATOR,
    
    'resourcePath' => __DIR__ . '/resources' . DIRECTORY_SEPARATOR,
    'resourceUrl'   => dirname($_SERVER['SCRIPT_NAME']) . '/Superglue/resources/',
    
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