<?php

// This is global bootstrap for autoloading
\Codeception\Util\Autoload::addNamespace(
    'Nip',
    dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'src'
);

require dirname(__DIR__) . '/vendor/autoload.php';