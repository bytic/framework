<?php

/**
 * From Symfony Symfony\Component\HttpFoundation\Tests\HttpTest class.
 */

namespace Nip\Tests\Unit\Request;

use Nip\Request;

/**
 * Class HttpTest.
 */
class HttpTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @dataProvider getBaseUrlData
     */
    public function testGetBaseUrl($uri, $server, $expectedBaseUrl, $expectedPathInfo)
    {
        $request = Request::create($uri, 'GET', [], [], [], $server);
        static::assertSame($expectedBaseUrl, $request->getHttp()->getBaseUrl(), 'baseUrl');
        static::assertSame($expectedPathInfo, $request->getPathInfo(), 'pathInfo');
    }

    // tests

    /**
     * @return array
     */
    public function getBaseUrlData()
    {
        return [
            [
                '/fruit/strawberry/1234index.php/blah',
                [
                    'SCRIPT_FILENAME' => 'E:/Sites/cc-new/public_html/fruit/index.php',
                    'SCRIPT_NAME'     => '/fruit/index.php',
                    'PHP_SELF'        => '/fruit/index.php',
                ],
                '/fruit',
                '/strawberry/1234index.php/blah',
            ],
            [
                '/fruit/strawberry/1234index.php/blah',
                [
                    'SCRIPT_FILENAME' => 'E:/Sites/cc-new/public_html/index.php',
                    'SCRIPT_NAME'     => '/index.php',
                    'PHP_SELF'        => '/index.php',
                ],
                '',
                '/fruit/strawberry/1234index.php/blah',
            ],
            [
                '/foo%20bar/',
                [
                    'SCRIPT_FILENAME' => '/home/John Doe/public_html/foo bar/app.php',
                    'SCRIPT_NAME'     => '/foo bar/app.php',
                    'PHP_SELF'        => '/foo bar/app.php',
                ],
                '/foo%20bar',
                '/',
            ],
            [
                '/foo%20bar/home',
                [
                    'SCRIPT_FILENAME' => '/home/John Doe/public_html/foo bar/app.php',
                    'SCRIPT_NAME'     => '/foo bar/app.php',
                    'PHP_SELF'        => '/foo bar/app.php',
                ],
                '/foo%20bar',
                '/home',
            ],
            [
                '/foo%20bar/app.php/home',
                [
                    'SCRIPT_FILENAME' => '/home/John Doe/public_html/foo bar/app.php',
                    'SCRIPT_NAME'     => '/foo bar/app.php',
                    'PHP_SELF'        => '/foo bar/app.php',
                ],
                '/foo%20bar/app.php',
                '/home',
            ],
            [
                '/foo%20bar/app.php/home%3Dbaz',
                [
                    'SCRIPT_FILENAME' => '/home/John Doe/public_html/foo bar/app.php',
                    'SCRIPT_NAME'     => '/foo bar/app.php',
                    'PHP_SELF'        => '/foo bar/app.php',
                ],
                '/foo%20bar/app.php',
                '/home%3Dbaz',
            ],
            [
                '/foo/bar+baz',
                [
                    'SCRIPT_FILENAME' => '/home/John Doe/public_html/foo/app.php',
                    'SCRIPT_NAME'     => '/foo/app.php',
                    'PHP_SELF'        => '/foo/app.php',
                ],
                '/foo',
                '/bar+baz',
            ],
        ];
    }

    public function testGetUri()
    {
        $request = Request::create('http://test.com/foo?bar=baz');
        static::assertEquals('http://test.com/foo?bar=baz', $request->getHttp()->getUri());
    }
}
