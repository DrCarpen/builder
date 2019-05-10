# Bilder
### 介绍
> 以命令行模式生成对应的 `Model,Trait,Struct,Service,Logic,Controller`文件,自动生成“CURD”
的接口模板



### 使用方法
##### 1 composer.json引入如下包名，更新composer

```bash
"require-dev" : {
        "drcarpen/builder":"^0.4"
    }
```
#####  2 app/Commands 加入新文件 BuilderCommand.php
```bash

<?php
namespace App\Commands;

use Uniondrug\Builder\Commands\Builder;

/**
 * 生成脚手架
 * php console builder --table tableName
 * @package App\Commands
 */
class BuilderCommand extends Builder
{
    protected $authorConfig = [
        'name' => 'yourName',
        'email' => 'yourEmail@uniondrug.cn'
    ];
}

```

##### 3 命令行第三个参数为数据表命，必须依照规范，为下划线定义，如 wx_members

```bash

php console builder --table tableName --e release

```

### 参数说明

1. --table tableName 表名必填，根据此表名生成对应的Model等文件(默认使用database.php中的配置)
1. --env     environment 指定的环境变量，可改变database.php中的对应环境的数据库配置

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



