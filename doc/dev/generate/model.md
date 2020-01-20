# 模型生成

生成数据库中所有表的模型文件，如果设置了`include`或`exclude`，则按照相应规则过滤表。

必选参数：
`-namespace` 生成的Model所在命名空间

可选参数：
`-database` 数据库名，不传则取连接池默认配置的库名
`-poolName` 连接池名称，不传则取默认连接池
`-prefix` 传值则去除该表前缀
`-include` 要包含的表名，以半角逗号分隔
`-exclude` 要排除的表名，以半角逗号分隔
`-override` 是否覆盖已存在的文件，请慎重！`true`-全覆盖;`false`-不覆盖;`base`-覆盖基类;`model`-覆盖模型类;默认缺省状态为`false`
`-config` 配置文件。`true`-项目配置；`false`-忽略配置；php配置文件名-使用该配置文件。默认为`true`
`-basePath` 指定命名空间对应的基准路径，可选
`-entity` 序列化时是否使用驼峰命名(`true` or `false`),默认`true`,可选

示例：

```
HttpDemo/bin/imi generate/model -namespace "命名空间"
```
