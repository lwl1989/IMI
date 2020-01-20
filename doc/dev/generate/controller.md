# 控制器生成

生成一个 Http Controller

必选参数：
`-name` 生成的 Controller 类名
`-namespace` 生成的 Controller 所在命名空间

可选参数：
`-prefix` 路由前缀，不传则为类名
`-render` 渲染方式，默认为json，可选：html/json/xml
`-rest` 是否生成 RESTful 风格，默认 false
`-override` 是否覆盖已存在的文件，请慎重！(true/false)

示例：
```
HttpDemo/bin/imi generate/httpController -name 控制器名 -namespace "命名空间"

HttpDemo/bin/imi generate/httpController -name 控制器名 -namespace "命名空间" -rest
```