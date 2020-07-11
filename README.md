## Builder v2.0

### 介绍
1. 致力于开发全流程的代码生成工具
1. 支持单接口模式，可自定义接口
1. 支持model模式
1. 一行命令，让你的CURD生活更加简单


### 使用方法
##### 1 composer.json引入如下包名，更新composer
```text
"require-dev" : {
        "drcarpen/builder":"^2.0"
    }
```
#####  2 app/Commands 加入新文件 BuilderCommand.php
```text

<?php
namespace App\Commands;

use Uniondrug\Builder\Commands\Builder;

class BuilderCommand extends Builder
{
}
```


##### 3 命令行第三个参数为数据表命，必须依照规范，为下划线定义，如 wx_members

```bash

php console builder --table tableName -e release

```

### 参数说明

1. --table tableName 表名必填，根据此表名生成对应的Model等文件(默认使用database.php中的配置)
1. -e     environment 指定的环境变量，可改变database.php中的对应环境的数据库配置

### 功能说明

####  Model层
1. 根据指定的数据表生成对应的Model文件
1. 生成property属性
1. model含有`status`字段时，自动生成对应的`statusText`

#### Struct层
1. 生成对应的`trait`文件
1. 生成 `create，delete，update，detail，listing，paging`六个入参结构体
1. 生成`row，rows，listing`三个出参结构体

#### Service层
1. 生成`create，delete，update，detail，listing，paging`的方法

#### Logic层
1. 生成`create，delete，update，detail，listing，paging`的逻辑层文件

#### Controller层
1. 生成`create，delete，update，detail，listing，paging`的方法及sdk名

#### 版本更新计划
##### v1.1版本
1. 已知bug修复
1. 支持多model生成，baseModel与normalModel拆分，支持重写
1. 支持数据字段注解，支持@enum(1=a|2=b)的文档显示
1. 支持单model重写 --model all
                 --model  tableName


