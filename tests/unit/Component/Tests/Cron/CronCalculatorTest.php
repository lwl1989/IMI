<?php
namespace Imi\Test\Component\Tests\Cron;

use Imi\App;
use Imi\Cron\CronRule;
use Imi\Test\BaseTest;

/**
 * @testdox CronCalculator
 */
class CronCalculatorTest extends BaseTest
{
    /**
     * CronCalculator
     *
     * @var \Imi\Cron\CronCalculator
     */
    private $cronCalculator;

    public function startTest()
    {
        parent::startTest();
        $this->cronCalculator = App::getBean('CronCalculator');
    }

    public function testYear()
    {
        $beginTime = strtotime('2018-06-21 12:34:56');
        $this->assertEquals(strtotime('2018-06-21 12:34:57'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['year' => '*']),
        ]));
        $this->assertEquals(strtotime('2018-06-21 12:34:57'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['year' => '2018']),
        ]));
        $this->assertEquals(strtotime('2019-01-01 00:00:00'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['year' => '2017,2019']),
        ]));
        $this->assertEquals(strtotime('2019-01-01 00:00:01'), $this->cronCalculator->getNextTickTime(strtotime('2019-01-01 00:00:00'), [
            new CronRule(['year' => '2019-2020']),
        ]));
        $this->assertEquals(strtotime('2020-06-21 12:34:56'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['year' => '2n']),
        ]));
    }

    public function testMonth()
    {
        $beginTime = strtotime('2018-06-21 12:34:56');
        $this->assertEquals(strtotime('2018-06-21 12:34:57'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['month' => '*']),
        ]));
        $this->assertEquals(strtotime('2018-07-01 00:00:00'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['month' => '7']),
        ]));
        $this->assertEquals(strtotime('2018-12-01 00:00:00'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['month' => '-1']),
        ]));
        $this->assertEquals(strtotime('2018-06-21 12:34:57'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['month' => '5,6']),
        ]));
        $this->assertEquals(strtotime('2018-07-01 00:00:00'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['month' => '7-9']),
        ]));
        $this->assertEquals(strtotime('2018-11-01 00:00:00'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['month' => '-2--1']),
        ]));
        $this->assertEquals(strtotime('2018-09-21 12:34:56'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['month' => '3n']),
        ]));
    }

    public function testWeek()
    {
        $beginTime = strtotime('2018-06-21 12:34:56');
        $this->assertEquals(strtotime('2018-06-21 12:34:57'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['week' => '*']),
        ]));
        $this->assertEquals(strtotime('2018-06-22 00:00:00'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['week' => '5']),
        ]));
        $this->assertEquals(strtotime('2018-06-24 00:00:00'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['week' => '-1']),
        ]));
        $this->assertEquals(strtotime('2018-06-23 00:00:00'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['week' => '3,6']),
        ]));
        $this->assertEquals(strtotime('2018-06-22 00:00:00'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['week' => '5-7']),
        ]));
        $this->assertEquals(strtotime('2018-06-23 00:00:00'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['week' => '-2--1']),
        ]));
    }


    public function testDay()
    {
        $beginTime = strtotime('2018-06-21 12:34:56');
        $this->assertEquals(strtotime('2018-06-21 12:34:57'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['day' => '*']),
        ]));

        $this->assertEquals(strtotime('2018-07-11 00:00:00'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['day' => '11']),
        ]));
        $this->assertEquals(strtotime('2018-01-31 00:00:00'), $this->cronCalculator->getNextTickTime(strtotime('2018-01-01'), [
            new CronRule(['day' => '-1']),
        ]));
        $this->assertEquals(strtotime('2018-02-28 00:00:00'), $this->cronCalculator->getNextTickTime(strtotime('2018-02-02'), [
            new CronRule(['day' => '-1']),
        ]));
        $this->assertEquals(strtotime('2016-02-29 00:00:00'), $this->cronCalculator->getNextTickTime(strtotime('2016-02-02'), [
            new CronRule(['day' => '-1']),
        ]));
        $this->assertEquals(strtotime('2018-04-30 00:00:00'), $this->cronCalculator->getNextTickTime(strtotime('2018-04-04'), [
            new CronRule(['day' => '-1']),
        ]));
        $this->assertEquals(strtotime('2018-07-05 00:00:00'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['day' => '5,6']),
        ]));
        $this->assertEquals(strtotime('2018-07-07 00:00:00'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['day' => '7-9']),
        ]));
        $this->assertEquals(strtotime('2018-06-29 00:00:00'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['day' => '-2--1']),
        ]));
        $this->assertEquals(strtotime('2018-06-24 12:34:56'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['day' => '3n']),
        ]));

        $this->assertEquals(strtotime('2019-01-11 00:00:00'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['day' => 'year 11']),
        ]));
        $this->assertEquals(strtotime('2018-12-31 00:00:00'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['day' => 'year -1']),
        ]));
        $this->assertEquals(strtotime('2019-01-05 00:00:00'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['day' => 'year 5,6']),
        ]));
        $this->assertEquals(strtotime('2019-01-07 00:00:00'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['day' => 'year 7-9']),
        ]));
        $this->assertEquals(strtotime('2018-12-30 00:00:00'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['day' => 'year -2--1']),
        ]));
    }

    public function testHour()
    {
        $beginTime = strtotime('2018-06-21 12:34:56');
        $this->assertEquals(strtotime('2018-06-21 12:34:57'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['hour' => '*']),
        ]));

        $this->assertEquals(strtotime('2018-06-22 11:00:00'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['hour' => '11']),
        ]));
        $this->assertEquals(strtotime('2018-06-21 23:00:00'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['hour' => '-1']),
        ]));
        $this->assertEquals(strtotime('2018-06-22 05:00:00'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['hour' => '5,6']),
        ]));
        $this->assertEquals(strtotime('2018-06-22 07:00:00'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['hour' => '7-9']),
        ]));
        $this->assertEquals(strtotime('2018-06-21 22:00:00'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['hour' => '-2--1']),
        ]));
        $this->assertEquals(strtotime('2018-06-21 15:34:56'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['hour' => '3n']),
        ]));
    }

    public function testMinute()
    {
        $beginTime = strtotime('2018-06-21 12:34:56');
        $this->assertEquals(strtotime('2018-06-21 12:34:57'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['minute' => '*']),
        ]));

        $this->assertEquals(strtotime('2018-06-21 13:11:00'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['minute' => '11']),
        ]));
        $this->assertEquals(strtotime('2018-06-21 12:59:00'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['minute' => '-1']),
        ]));
        $this->assertEquals(strtotime('2018-06-21 13:05:00'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['minute' => '5,6']),
        ]));
        $this->assertEquals(strtotime('2018-06-21 13:07:00'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['minute' => '7-9']),
        ]));
        $this->assertEquals(strtotime('2018-06-21 12:58:00'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['minute' => '-2--1']),
        ]));
        $this->assertEquals(strtotime('2018-06-21 12:37:56'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['minute' => '3n']),
        ]));
    }

    public function testSecond()
    {
        $beginTime = strtotime('2018-06-21 12:34:56');
        $this->assertEquals(strtotime('2018-06-21 12:34:57'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['second' => '*']),
        ]));

        $this->assertEquals(strtotime('2018-06-21 12:35:11'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['second' => '11']),
        ]));
        $this->assertEquals(strtotime('2018-06-21 12:34:59'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['second' => '-1']),
        ]));
        $this->assertEquals(strtotime('2018-06-21 12:35:05'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['second' => '5,6']),
        ]));
        $this->assertEquals(strtotime('2018-06-21 12:35:07'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['second' => '7-9']),
        ]));
        $this->assertEquals(strtotime('2018-06-21 12:34:58'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['second' => '-2--1']),
        ]));
        $this->assertEquals(strtotime('2018-06-21 12:34:59'), $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['second' => '3n']),
        ]));
    }
    
    public function testAll()
    {
        $beginTime = strtotime('2018-06-21 12:34:56');
        // 每天 0 点执行一次
        $this->assertEquals(strtotime('2018-06-22 00:00:00'), $lastTime = $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['hour' => '0', 'minute' => '0', 'second' => '0']),
        ]));
        $this->assertEquals(strtotime('2018-06-23 00:00:00'), $lastTime = $this->cronCalculator->getNextTickTime($lastTime, [
            new CronRule(['hour' => '0', 'minute' => '0', 'second' => '0']),
        ]));

        // 每 15 分钟执行一次
        $this->assertEquals(strtotime('2018-06-21 12:49:56'), $lastTime = $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['minute' => '15n']),
        ]));
        $this->assertEquals(strtotime('2018-06-21 13:04:56'), $lastTime = $this->cronCalculator->getNextTickTime($lastTime, [
            new CronRule(['minute' => '15n']),
        ]));

        // 每周一中午 12 点执行
        $this->assertEquals(strtotime('2018-06-25 12:00:00'), $lastTime = $this->cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['week' => '1', 'hour' => '12', 'minute' => '0', 'second' => '0']),
        ]));
        $this->assertEquals(strtotime('2018-07-02 12:00:00'), $lastTime = $this->cronCalculator->getNextTickTime($lastTime, [
            new CronRule(['week' => '1', 'hour' => '12', 'minute' => '0', 'second' => '0']),
        ]));

        // 每月倒数第 3 天中午 12 点
        $this->assertEquals(strtotime('2019-02-26 12:00:00'), $lastTime = $this->cronCalculator->getNextTickTime(strtotime('2019-01-31 12:00:00'), [
            new CronRule(['day' => '-3', 'hour' => '12', 'minute' => '0', 'second' => '0']),
        ]));
        $this->assertEquals(strtotime('2016-02-27 12:00:00'), $lastTime = $this->cronCalculator->getNextTickTime(strtotime('2016-01-31 12:00:00'), [
            new CronRule(['day' => '-3', 'hour' => '12', 'minute' => '0', 'second' => '0']),
        ]));
    }

}
