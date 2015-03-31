<?php

return array(
    'callbackPrefix' => isset($_ENV['SUPERGLUE_CALLBACK']) ? $_ENV['SUPERGLUE_CALLBACK'] : 'Superglue:/',
    
    'publicBase' => realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR,
    
    'url' => isset($_ENV['SUPERGLUE_BASE']) ? $_ENV['SUPERGLUE_CALLBACK'] : str_replace('//','/',dirname($_SERVER['SCRIPT_NAME']) . '/'),
    
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