<?php
namespace Imi\Cron;

use Imi\Bean\Annotation\Bean;

/**
 * 定时规则计算器
 * 
 * @Bean("CronCalculator")
 */
class CronCalculator
{
    /**
     * 获取下一次执行时间
     *
     * @param int $lastTime
     * @param \Imi\Cron\CronRule[] $cronRules
     * @return int
     */
    public function getNextTickTime($lastTime, array $cronRules)
    {
        $times = [];
        foreach($cronRules as $cronRule)
        {
            if($result = $this->parseN($cronRule, $lastTime))
            {
                $times[] = $result;
                continue;
            }

            $years = $this->getAllYear($cronRule->getYear(), $lastTime);
            $months = $this->getAllMonth($cronRule->getMonth(), $lastTime);
            $weeks = $this->getAllWeek($cronRule->getWeek(), $lastTime);
            $days = $this->getAllDay($cronRule->getDay(), $lastTime);
            $hours = $this->getAllHour($cronRule->getHour(), $lastTime);
            $minutes = $this->getAllMinute($cronRule->getMinute(), $lastTime);
            $seconds = $this->getAllSecond($cronRule->getSecond(), $lastTime);
            $times[] = $this->generateTime($lastTime, $years, $months, $weeks, $days, $hours, $minutes, $seconds);
        }
        if(isset($times[1]))
        {
            return min(...$times);
        }
        else
        {
            return $times[0] ?? null;
        }
    }

    private function generateTime($lastTime, $years, $months, $weeks, $days, $hours, $minutes, $seconds)
    {
        if($lastTime < 0)
        {
            $lastTime = time();
        }
        $nowYear = date('Y', $lastTime);
        $nowMonth = date('m', $lastTime);
        $nowDay = date('d', $lastTime);
        $nowHour = date('H', $lastTime);
        $nowMinute = date('i', $lastTime);
        $nowSecond = date('s', $lastTime);
        foreach($years as $year)
        {
            if($year < $nowYear)
            {
                continue;
            }
            foreach($months as $month)
            {
                if($year == $nowYear && $month < $nowMonth)
                {
                    continue;
                }
                foreach($days as $day)
                {
                    if('year' === $day)
                    {
                        continue;
                    }
                    if('year' === $days[0])
                    {
                        if($day < 0)
                        {
                            $timestamp = strtotime($year . '-12-31') + 86400 * ((int)$day + 1);
                        }
                        else
                        {
                            $timestamp = strtotime($year . '-01-01') + 86400 * ((int)$day - 1);
                        }
                        [$y, $m, $d] = explode('-', date('Y-m-d', $timestamp));
                        if(($y == $nowYear && (($m == $nowMonth && $d < $nowDay) || ($m < $nowMonth))) || !in_array(date('N', $timestamp), $weeks))
                        {
                            continue;
                        }
                        $result = $this->parseHis($y, $m, $d, $hours, $minutes, $seconds, $nowYear, $nowMonth, $nowDay, $nowHour, $nowMinute, $nowSecond);
                    }
                    else
                    {
                        if($day < 0)
                        {
                            $day = date('d', strtotime($year . '-' . $month . '-' . date('t', strtotime($year . '-' . $month . '-01'))) + 86400 * ((int)$day + 1));
                        }
                        if(($year == $nowYear && $month == $nowMonth && $day < $nowDay) || !in_array(date('N', strtotime("{$year}-{$month}-{$day}")), $weeks))
                        {
                            continue;
                        }
                        $result = $this->parseHis($year, $month, $day, $hours, $minutes, $seconds, $nowYear, $nowMonth, $nowDay, $nowHour, $nowMinute, $nowSecond);
                    }
                    if(null !== $result)
                    {
                        return $result;
                    }
                }
            }
        }
    }

    private function parseHis($year, $month, $day, $hours, $minutes, $seconds, $nowYear, $nowMonth, $nowDay, $nowHour, $nowMinute, $nowSecond)
    {
        foreach($hours as $hour)
        {
            if($year == $nowYear && $month == $nowMonth && $day == $nowDay && $hour < $nowHour)
            {
                continue;
            }
            foreach($minutes as $minute)
            {
                if($year == $nowYear && $month == $nowMonth && $day == $nowDay && $hour == $nowHour && $minute < $nowMinute)
                {
                    continue;
                }
                foreach($seconds as $second)
                {
                    if($year == $nowYear && $month == $nowMonth && $day == $nowDay && $hour == $nowHour && $minute == $nowMinute && $second <= $nowSecond)
                    {
                        continue;
                    }
                    return strtotime("{$year}-{$month}-{$day} {$hour}:{$minute}:{$second}");
                }
            }
        }
    }

    public function getAll($rule, $name, $min, $max, $dateFormat, $lastTime)
    {
        // 所有
        if('*' === $rule)
        {
            return range($min, $max);
        }
        // 区间
        if(strpos($rule, '-') > 0)
        {
            [$begin, $end] = explode('-', substr($rule, 1), 2);
            $begin = substr($rule, 0, 1) . $begin;
            if('day' !== $name)
            {
                // 负数支持
                if($begin < $min)
                {
                    $begin = $max + 1 + (int)$begin;
                }
                if($end < $min)
                {
                    $end = $max + 1 + (int)$end;
                }
            }
            return range(max($min, $begin), min($end, $max));
        }
        // 步长
        if('n' === substr($rule, -1, 1))
        {
            $step = (int)substr($rule, 0, -1);
            if($lastTime < $min)
            {
                return range($min, $max, $step);
            }
            else
            {
                $s = date($dateFormat, $lastTime);
                return range($s % $step, $max, $step);
            }
        }
        // 列表
        $list = explode(',', $rule);
        if('day' !== $name)
        {
            // 处理负数
            foreach($list as &$item)
            {
                if($item < $min)
                {
                    $item = $max + 1 + (int)$item;
                }
            }
        }
        // 从小到大排序
        sort($list, SORT_NUMERIC);
        return $list;
    }

    /**
     * 获取所有月份可能性
     *
     * @param string $year
     * @param int $lastTime
     * @return void
     */
    public function getAllYear($year, $lastTime)
    {
        $min = date('Y', $lastTime);
        $max = 2100; // 我觉得 2100 年不可能还在用这个代码了吧……
        return $this->getAll($year, 'year', $min, $max, 'Y', $lastTime);
    }

    /**
     * 获取所有月份可能性
     *
     * @param string $month
     * @param int $lastTime
     * @return void
     */
    public function getAllMonth($month, $lastTime)
    {
        return $this->getAll($month, 'month', 1, 12, 'm', $lastTime);
    }

    /**
     * 获取所有日期可能性
     *
     * @param string $day
     * @param int $lastTime
     * @return void
     */
    public function getAllDay($day, $lastTime)
    {
        if('year ' === substr($day, 0, 5))
        {
            $day = substr($day, 5);
            $list = $this->getAll($day, 'day', 1, 366, 'd', $lastTime);
            array_unshift($list, 'year');
        }
        else
        {
            $list = $this->getAll($day, 'day', 1, 31, 'd', $lastTime);
        }
        $negatives = [];
        foreach($list as $i => $value)
        {
            if($value < 0)
            {
                $negatives[] = $value;
                unset($list[$i]);
            }
            else
            {
                break;
            }
        }
        rsort($negatives, SORT_NUMERIC);
        foreach($negatives as $value)
        {
            $list[] = $value;
        }
        return array_values($list);
    }

    /**
     * 获取所有周的可能性
     *
     * @param string $week
     * @param int $lastTime
     * @return void
     */
    public function getAllWeek($week, $lastTime)
    {
        return $this->getAll($week, 'week', 1, 7, 'N', $lastTime);
    }

    /**
     * 获取所有小时可能性
     *
     * @param string $hour
     * @param int $lastTime
     * @return void
     */
    public function getAllHour($hour, $lastTime)
    {
        return $this->getAll($hour, 'hour', 0, 23, 'H', $lastTime);
    }

    /**
     * 获取所有分钟可能性
     *
     * @param string $minute
     * @param int $lastTime
     * @return void
     */
    public function getAllMinute($minute, $lastTime)
    {
        return $this->getAll($minute, 'minute', 0, 59, 'i', $lastTime);
    }

    /**
     * 获取所有秒数可能性
     *
     * @param string $second
     * @param int $lastTime
     * @return void
     */
    public function getAllSecond($second, $lastTime)
    {
        return $this->getAll($second, 'second', 0, 59, 's', $lastTime);
    }

    /**
     * 处理 2n、3n……格式
     *
     * @param \Imi\Cron\CronRule $cronRule
     * @param int $lastTime
     * @return void
     */
    private function parseN($cronRule, $lastTime)
    {
        if($lastTime < 0)
        {
            return false;
        }
        if('n' === substr($cronRule->getSecond(), -1, 1))
        {
            return $lastTime + substr($cronRule->getSecond(), 0, -1);
        }
        if('n' === substr($cronRule->getMinute(), -1, 1))
        {
            return $lastTime + substr($cronRule->getMinute(), 0, -1) * 60;
        }
        if('n' === substr($cronRule->getHour(), -1, 1))
        {
            return $lastTime + substr($cronRule->getHour(), 0, -1) * 3600;
        }
        if('n' === substr($cronRule->getDay(), -1, 1))
        {
            return $lastTime + substr($cronRule->getDay(), 0, -1) * 86400;
        }
        if('n' === substr($cronRule->getWeek(), -1, 1))
        {
            return $lastTime + substr($cronRule->getWeek(), 0, -1) * 604800;
        }
        if('n' === substr($cronRule->getMonth(), -1, 1))
        {
            return strtotime('+' . substr($cronRule->getMonth(), 0, -1) . ' month', $lastTime);
        }
        if('n' === substr($cronRule->getYear(), -1, 1))
        {
            return strtotime('+' . substr($cronRule->getYear(), 0, -1) . ' year', $lastTime);
        }
        return false;
    }

}
