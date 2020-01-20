<?php
namespace Imi\Test\Component\Tests;

use Imi\Util\Imi;
use Imi\Util\File;
use Imi\Test\BaseTest;
use PHPUnit\Framework\Assert;
use Imi\App;

/**
 * @testdox Lock Annotation
 */
class LockAnnotationTest extends BaseTest
{
    public function test()
    {
        $test = App::getBean('TestLockAnnotation');
        $time = microtime(true);
        $throwables = [];
        $channel = new \Swoole\Coroutine\Channel(3);
        for($i = 0; $i < 3; ++$i)
        {
            $throwables[] = null;
            $index = $i;
            go(function() use(&$throwables, $index, $test, $channel){
                try {
                    $test->test();
                } catch(\Throwable $th) {
                    $throwables[$index] = $th;
                } finally {
                    $channel->push(1);
                }
            });
        }
        $count = 0;
        while($ret = $channel->pop())
        {
            if(1 === $ret)
            {
                ++$count;
                if($count >= 3)
                {
                    break;
                }
            }
        }
        $useTime = microtime(true) - $time;
        foreach($throwables as $th)
        {
            if($th)
            {
                throw $th;
            }
        }
        $channel->close();
        Assert::assertGreaterThan(0.3, $useTime);
    }

    public function testAfterLock()
    {
        $test = App::getBean('TestLockAnnotation');
        Assert::assertEquals(2, $test->index());
        Assert::assertEquals(3, $test->index2());
    }

}
