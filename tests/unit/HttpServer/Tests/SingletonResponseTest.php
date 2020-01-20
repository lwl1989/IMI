<?php
namespace Imi\Test\HttpServer\Tests;

use Yurun\Util\HttpRequest;

/**
 * @testdox SingletonResponseTest
 */
class SingletonResponseTest extends BaseTest
{
    /**
     * Response1
     *
     * @return void
     */
    public function testResponse1()
    {
        $http = new HttpRequest;
        $response = $http->get($this->host . 'singletonResponse1');
        $this->assertEquals('imi niubi-1', $response->body());
    }

    /**
     * Response2
     *
     * @return void
     */
    public function testResponse2()
    {
        $http = new HttpRequest;
        $response = $http->get($this->host . 'singletonResponse2');
        $this->assertEquals('imi niubi-2', $response->body());
    }

}