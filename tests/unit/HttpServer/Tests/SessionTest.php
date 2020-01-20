<?php
namespace Imi\Test\HttpServer\Tests;

use Yurun\Util\HttpRequest;
use PHPUnit\Framework\Assert;

/**
 * @testdox HttpSession FileHandler
 */
class SessionTest extends BaseTest
{
    public function testSetGetDelete()
    {
        $http = new HttpRequest;
        $http->get($this->host . 'session/login');

        $response = $http->get($this->host . 'session/status');
        $data = $response->json(true);
        Assert::assertTrue(isset($data['isLogin']) && $data['isLogin']);
        Assert::assertEquals('admin', $data['username']);

        $http->get($this->host . 'session/logout');

        $response = $http->get($this->host . 'session/status');
        $data = $response->json(true);
        Assert::assertTrue(isset($data['isLogin']) && !$data['isLogin']);
        Assert::assertArrayNotHasKey('username', $data);

    }

    public function testOnce()
    {
        $http = new HttpRequest;

        $response = $http->get($this->host . 'session/verifySms?vcode=1234');
        $data = $response->json(true);
        Assert::assertTrue(isset($data['success']) && !$data['success']);

        $http->get($this->host . 'session/sendSms');

        $response = $http->get($this->host . 'session/verifySms?vcode=1234');
        $data = $response->json(true);
        Assert::assertTrue(isset($data['success']) && $data['success']);

        $response = $http->get($this->host . 'session/verifySms?vcode=1234');
        $data = $response->json(true);
        Assert::assertTrue(isset($data['success']) && !$data['success']);
    }

}