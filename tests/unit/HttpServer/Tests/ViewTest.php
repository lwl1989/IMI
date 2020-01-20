<?php
namespace Imi\Test\HttpServer\Tests;

use Yurun\Util\HttpRequest;
use PHPUnit\Framework\Assert;

/**
 * @testdox Http View
 */
class ViewTest extends BaseTest
{
    public function testHtml()
    {
        $http = new HttpRequest;
        $time = time();
        $response = $http->get($this->host . 'html?time=' . time());
        Assert::assertEquals('<p>' . date('Y-m-d H:i:s', $time) . '</p>', $response->body());
    }

    public function testHtml2()
    {
        $http = new HttpRequest;
        $time = time();
        $response = $http->get($this->host . 'html2?time=' . time());
        Assert::assertEquals('<p>tpl2:' . date('Y-m-d H:i:s', $time) . '</p>', $response->body());
    }

    public function testJson()
    {
        $http = new HttpRequest;
        $time = time();
        $response = $http->get($this->host . 'json?time=' . time());
        Assert::assertEquals([
            'time'  =>  $time . '',
            'data'  =>  'now: ' . date('Y-m-d H:i:s', $time),
        ], $response->json(true));
    }

}