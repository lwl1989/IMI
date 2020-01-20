<?php
namespace Imi\Test\HttpServer\Tests;

use Yurun\Util\HttpRequest;
use Yurun\Util\YurunHttp\Http\Psr7\UploadedFile;
use Imi\Util\Http\Consts\MediaType;

/**
 * @testdox SuperGlobalsTest
 */
class SuperGlobalsTest extends BaseTest
{
    /**
     * test
     *
     * @return void
     */
    public function testSuperGlobals()
    {
        $http = new HttpRequest;
        $response = $http->get($this->host . 'session/login');
        $sessionId = $response->getCookie('imisid');

        $file = new UploadedFile(basename(__FILE__), MediaType::TEXT_HTML, __FILE__);
        $http->content([
            'data'  =>  'imi',
            'file'  =>  $file,
        ]);
        $response = $http->post($this->host . 'superGlobalsInfo?id=1&name=abc');
        $data = $response->json(true);

        $this->assertEquals([
            'id'    =>  1,
            'name'  =>  'abc',
        ], $data['get'] ?? null);

        $this->assertEquals([
            'data'    =>  'imi',
        ], $data['post'] ?? null);

        $this->assertEquals([
            'id'    =>  1,
            'name'  =>  'abc',
            'data'  =>  'imi',
            'imisid'=>  $sessionId,
        ], $data['request'] ?? null);

        $this->assertEquals([
            'imisid'=>  $sessionId,
        ], $data['cookie'] ?? null);

        $this->assertEquals([
            'auth'  =>  [
                'username'  =>  'admin',
            ],
        ], $data['session'] ?? null);

        $this->assertEquals('/superGlobalsInfo', $data['server']['REQUEST_URI'] ?? null);

        $content = file_get_contents(__FILE__);
        $this->assertArraySubset([
            'file'  =>  [
                'name'      =>  basename(__FILE__),
                'type'      =>  MediaType::TEXT_HTML,
                'error'     =>  0,
                'size'      =>  strlen($content),
            ],
        ], $data['files'] ?? null);

    }
}