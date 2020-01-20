<?php
namespace Imi\Db\Listener;

use Imi\Event\Event;
use Imi\Event\EventParam;
use Imi\Util\ImiPriority;
use Imi\Event\IEventListener;
use Imi\Bean\Annotation\Listener;

/**
 * @Listener(eventName="IMI.INITED", priority=ImiPriority::IMI_MAX)
 */
class WorkerStart implements IEventListener
{
    /**
     * 事件处理方法
     * @param InitEventParam $e
     * @return void
     */
    public function handle(EventParam $e)
    {
        Event::on('IMI.REQUEST_CONTENT.DESTROY', [new \Imi\Db\Listener\RequestContextDestroy, 'handle'], ImiPriority::IMI_MIN - 20);
    }

}
