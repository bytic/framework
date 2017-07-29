<?php

//<?php
//// Here you can initialize variables that will be available to your tests
//
//\Codeception\Util\Autoload::addNamespace(
//        'Nip\Tests',
//        dirname(__FILE__)
//    );
//
app()->share('inflector', new Nip\Inflector\Inflector());
app()->share('app', new \Nip\Application());


define('PROJECT_BASE_PATH', __DIR__ . '/..');
define('TEST_BASE_PATH', __DIR__);
define('TEST_FIXTURE_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'fixtures');


require dirname(__DIR__) . '/vendor/autoload.php';