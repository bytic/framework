<?php
// Here you can initialize variables that will be available to your tests

\Codeception\Util\Autoload::addNamespace(
    'Nip\Tests\Unit',
    dirname(__FILE__)
);

app()->share('inflector', new Nip\Inflector\Inflector());
app()->share('app', new \Nip\Application());
