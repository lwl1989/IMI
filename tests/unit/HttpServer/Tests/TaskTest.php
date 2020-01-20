<?php
namespace Imi\Test\HttpServer\Tests;

use Yurun\Util\HttpRequest;
use PHPUnit\Framework\Assert;

/**
 * @testdox Task
 */
class TaskTest extends BaseTest
{
    public function testTask()
    {
        $http = new HttpRequest;

        $response = $http->get($this->host . 'task/test');
        $data = $response->json(true);

        Assert::assertIsInt($data['post']);
        Assert::assertIsInt($data['nPost']);
        Assert::assertEquals('2019-06-21 00:00:00', $data['nPostWait']);
        Assert::assertEquals('2019-06-21 00:00:00', $data['postWait']);
        Assert::assertEquals([
            '2018-06-21 00:00:00',
            '2019-06-21 00:00:00',
        ], $data['postCo']);
    }
}
