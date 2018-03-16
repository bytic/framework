<?php

namespace Nip\Tests\I18n\Translator\Backend;

use Nip\I18n\Translator\Backend\File;
use Nip\Tests\AbstractTest;

class FileTest extends AbstractTest
{
    public function testAddLanguage()
    {
        $fileLoader = new File();
        $fileLoader->setBaseDirectory(TEST_FIXTURE_PATH . '/languages');

        $filedisk = $fileLoader->addLanguage('en');

        self::assertSame('Day', $filedisk->translate('day', 'en'));
    }
}
