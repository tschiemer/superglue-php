<?php


//define('IS_PHAR', (bool)Phar::running());
//define('__PHAR__', IS_PHAR ? Phar::running().DIRECTORY_SEPARATOR : __DIR__.DIRECTORY_SEPARATOR);
//if (defined('__ROOT__'))
//var_dump(__ROOT__);

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

set_exception_handler(function(\Exception $exception){
    echo 'Error ' . $exception->getCode() . ': ' . $exception->getMessage();
    exit;
});

//$cfgpath = __ROOT__ . '/superglue.config.php';
//if (file_exists($cfgpath)){
//    $config = new \Superglue\Config(require __ROOT__ . DIRECTORY_SEPARATOR . 'superglue.config.php');
//} else {
//    $config = new \Superglue\Config(require __DIR__ . DIRECTORY_SEPARATOR . 'config.default.php');
//    
//}


define('__ROOT__',__DIR__ . DIRECTORY_SEPARATOR );

$config =  new \Superglue\Config\Php( __ROOT__ .'Superglue.config.php' );

//use \Superglue\Server as Superglue;

//$superglue = new \Superglue\Server($config);
$superglue = new Superglue($config);

$superglue->init()->run();
