<?php

//ini_set('phar.readonly',1);


$pharName = 'superglue.phar';

$phar = new Phar($pharName);

$phar->buildFromDirectory( __DIR__ . '/src');

$phar->setStub(file_get_contents(__DIR__.'/stub.php'));

//$phar->setStub('<?php var_dump("First"); 
////require "phar://".__DIR__ ."/'.$pharName.'/lib.php";; __HALT_COMPILER();
//$phar->setStub(file_get_contents(__DIR__ . '/src/index.php'));

//var_dump($phar);