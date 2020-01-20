<?php
namespace Imi\Test;

use Swoole\Coroutine;
use Yurun\Doctrine\Common\Annotations\AnnotationReader;

abstract class AppBaseMain extends \Imi\Main\AppBaseMain
{
    public function __init()
    {
        // 这里可以做一些初始化操作，如果需要的话
        
        AnnotationReader::addGlobalIgnoredName('testdox');
        if(Coroutine::getuid() < 0 && version_compare(SWOOLE_VERSION, '4.4.4', '='))
        {
            \swoole_async_set([
                'max_thread_num'    =>  4,
            ]);
        }
    }

}