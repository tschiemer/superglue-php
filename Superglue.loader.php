<?php

/**
 * An example of a project-specific implementation.
 * 
 * After registering this autoload function with SPL, the following line
 * would cause the function to attempt to load the \Foo\Bar\Baz\Qux class
 * from /path/to/project/src/Baz/Qux.php:
 * 
 *      new \Foo\Bar\Baz\Qux;
 *      
 * @param string $class The fully-qualified class name.
 * @return void
 * @author http://www.php-fig.org/psr/psr-4/examples/
 */
spl_autoload_register(function ($class) {
    
    // project-specific namespace prefix
    $prefix = 'Superglue\\';

    // base directory for the namespace prefix
    $base_dir = __DIR__.DIRECTORY_SEPARATOR.'Superglue'.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR;

    if ($class == 'Superglue'){
        require $base_dir . 'Superglue' . '.php';
        return;
    }

    
    // does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }

    // get the relative class name
    $relative_class = substr($class, $len);

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $base_dir . str_replace('\\', DIRECTORY_SEPARATOR, $relative_class) . '.php';
    
    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
    
});

spl_autoload_register(function ($class) {
    $base_dir = __DIR__ . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR;
    
    $file = $base_dir . $class . '.php';
    if (file_exists($file)){
        require $file;
    }
});

set_exception_handler(function(\Superglue\Exceptions\Exception $exception){
    if ($exception instanceof \Superglue\Exceptions\NotFoundException){
        die('not found!');
    }
    echo 'Error ' . $exception->getCode() . ': ' . $exception->getMessage();
    exit;
});


define('__ROOT__', __DIR__ . DIRECTORY_SEPARATOR );

$config =  new \Superglue\Config\Php( __ROOT__ .'Superglue.config.php' );

$superglue = new Superglue($config);

$superglue->init()->run();
