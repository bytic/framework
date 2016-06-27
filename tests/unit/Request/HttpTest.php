<?php

namespace Nip\Tests\Request;

use Nip\Request;
use Nip\Request\Http;

class RequestTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _after()
    {
    }

    // tests
    /**
     * @dataProvider getBaseUrlData
     */
    public function testGetBaseUrl($uri, $server, $expectedBaseUrl, $expectedPathInfo)
    {
        $request = Request::create($uri, 'GET', array(), array(), array(), $server);
        $this->assertSame($expectedBaseUrl, $request->getHttp()->getBaseUrl(), 'baseUrl');
        $this->assertSame($expectedPathInfo, $request->getHttp()->getPathInfo(), 'pathInfo');
    }

    public function getBaseUrlData()
    {
        return array(
            array(
                '/fruit/strawberry/1234index.php/blah',
                array(
                    'SCRIPT_FILENAME' => 'E:/Sites/cc-new/public_html/fruit/index.php',
                    'SCRIPT_NAME' => '/fruit/index.php',
                    'PHP_SELF' => '/fruit/index.php',
                ),
                '/fruit',
                '/strawberry/1234index.php/blah',
            ),
            array(
                '/fruit/strawberry/1234index.php/blah',
                array(
                    'SCRIPT_FILENAME' => 'E:/Sites/cc-new/public_html/index.php',
                    'SCRIPT_NAME' => '/index.php',
                    'PHP_SELF' => '/index.php',
                ),
                '',
                '/fruit/strawberry/1234index.php/blah',
            ),
            array(
                '/foo%20bar/',
                array(
                    'SCRIPT_FILENAME' => '/home/John Doe/public_html/foo bar/app.php',
                    'SCRIPT_NAME' => '/foo bar/app.php',
                    'PHP_SELF' => '/foo bar/app.php',
                ),
                '/foo%20bar',
                '/',
            ),
            array(
                '/foo%20bar/home',
                array(
                    'SCRIPT_FILENAME' => '/home/John Doe/public_html/foo bar/app.php',
                    'SCRIPT_NAME' => '/foo bar/app.php',
                    'PHP_SELF' => '/foo bar/app.php',
                ),
                '/foo%20bar',
                '/home',
            ),
            array(
                '/foo%20bar/app.php/home',
                array(
                    'SCRIPT_FILENAME' => '/home/John Doe/public_html/foo bar/app.php',
                    'SCRIPT_NAME' => '/foo bar/app.php',
                    'PHP_SELF' => '/foo bar/app.php',
                ),
                '/foo%20bar/app.php',
                '/home',
            ),
            array(
                '/foo%20bar/app.php/home%3Dbaz',
                array(
                    'SCRIPT_FILENAME' => '/home/John Doe/public_html/foo bar/app.php',
                    'SCRIPT_NAME' => '/foo bar/app.php',
                    'PHP_SELF' => '/foo bar/app.php',
                ),
                '/foo%20bar/app.php',
                '/home%3Dbaz',
            ),
            array(
                '/foo/bar+baz',
                array(
                    'SCRIPT_FILENAME' => '/home/John Doe/public_html/foo/app.php',
                    'SCRIPT_NAME' => '/foo/app.php',
                    'PHP_SELF' => '/foo/app.php',
                ),
                '/foo',
                '/bar+baz',
            ),
        );
    }

    public function testGetUri()
    {
        $request = Request::create('http://test.com/foo?bar=baz');
        $this->assertEquals('http://test.com/foo?bar=baz', $request->getHttp()->getUri());
    }

}