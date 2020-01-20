# 开始一个新项目

## 项目初始化

创建 Http Server 项目：`composer create-project imiphp/project-http`

创建 WebSocket Server 项目：`composer create-project imiphp/project-websocket`

创建 TCP Server 项目：`composer create-project imiphp/project-tcp`

创建 UDP Server 项目：`composer create-project imiphp/project-udp`

> 如何运行请看上面项目中的`README.md`

## 流程说明

在IMI框架中，一个项目分为一个主服务器和多个子服务器。

其中，主服务器为必须，子服务器为可选。子服务器通过监听端口实现，一般不推荐开启过多的子服务器。

你需要为框架、每个服务器在其命名空间目录下都创建一个`Main.php`，并把类命名为`Main`

项目的`Main`必须继承`Imi\Main\AppBaseMain`类。

服务器的`Main`必须继承`Imi\Main\BaseMain`类。

并且实现一个`__init()`方法:

```php
public function __init()
{

}
```

你可以在里面做一些初始化的事情，不过大多数时候什么都不需要做。



