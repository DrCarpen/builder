## Builder v3.0.0
### Build More Code Easy

### 介绍
1. 涉及开发全流程的代码生成工具
1. 支持单纯model文件生成
1. 支持自定义单接口
1. 支持多数据选择



### 快速上手
##### 1 composer.json引入如下包名，更新composer

```text
"require-dev" : {
        "drcarpen/builder":"^3.0"
    }
```
#####  2 app/Commands目录下创建文件 BuilderCommand.php

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
php vendor/uniondrug/console/console builder  -e testing  -t 表名或控制器名 -a 方法
```

```bash
php vendor/uniondrug/console/console builder  --table=表名  --env=testing --api 方法
```

### 参数说明

1. --table(-t)     表名[必填]
1. --api(-a)       字段名[非必填]
1. --env(-e)       指定的环境变量[默认development]

### 功能说明

####  Model层
1. 根据指定的数据表生成对应的Model文件
1. 生成property属性注释
1. 生成columnMap()，支持表字段下划线式转化成小驼峰式 如user_staus映射成userStatus
1. 支持字段注释内容自动生成状态位，自动生成对应的[常量][映射方法][文本方法]`statusText`

#### Struct层
##### 入参结构体
1. 根据参数【-api -a】生成入参结构体，例如 --api create 生成 createRequest.php 文件
1. 若参数【--table -t】有对应的数据表，则入参结构体中生成入参字段
1. 自动注解Validator规则

##### 出参结构体
1. 根据参数【-api -a】生成出参结构体，例如 --api create 生成 createResult.php 文件
1. 分页列表：--api page 生成rowResult,pageResult
1. 无分页列表：--api listing 生成 rowResult，listingResult

##### Trait
1. 根据指定的数据表生成对应的`trait`文件

#### Service层
1. 根据参数【--table -t】生成service文件，例如 --table create 生成 createService.php 文件，并且注入到serviceTrait.php中
1. 根据参数【-api -a】生成方法

#### Logic层
1. 根据参数【-api -a】生成logic文件，例如 --api create 生成 createLogic.php 文件

#### Controller层
1. 根据参数【--table -t】生成对应的controller文件，例如 --table create 生成 createController.php 
1. 根据参数【-api -a】生成控制器中的方法，input,output,sdk注释等


### 版本更新计划

#### v3.0.0 版本
1. 不基于数据库的接口生产   -t clerk -a create
1. 基于数据库的model生产   -t clerk
1. 基于数据库的接口生产     -t clerk -a create
1. 结构体的入参出参，及模型的注释转为小驼峰 
1. 入参的类型加入validator判断
1. 模型的注释格式化




#### v2.0.0 版本
1. 多数据库连接支持 
1. 根据字段生成对应 [常量][映射方法][文本方法] 注意：该字段是以`status`或`type`结尾的字段
1. 字段注释参考  消息类型:1=特权通知|2=积分通知|3=到家通知|4=活动通知|5=公告|6=审核通知|7=意见反馈
1. App\Models\Abstracts\Model 添加 public static $_unknowText = '未知文本';
1. 生成表对应的columnMap() 把表字段下划线转化成驼峰式 如user_staus映射成userStatus;
```text
    php console builder --database=databases.db1 --table=message --column=type

    class Message extends Model
    {
    	// 消息类型
    	const TYPE_1 = 1; //特权通知
    	const TYPE_2 = 2; //积分通知
    	const TYPE_3 = 3; //到家通知
    	const TYPE_4 = 4; //活动通知
    	const TYPE_5 = 5; //公告
    	const TYPE_6 = 6; //审核通知
    	const TYPE_7 = 7; //意见反馈
        // 消息类型映射
        private static $_typeMap = [
            self::TYPE_1 => '特权通知',
            self::TYPE_2 => '积分通知',
            self::TYPE_3 => '到家通知',
            self::TYPE_4 => '活动通知',
            self::TYPE_5 => '公告',
            self::TYPE_6 => '审核通知',
            self::TYPE_7 => '意见反馈'
        ];
    
        /**
         * 消息类型文本
         * return string
         */
        public function getTypeText()
        {
            return static::$_typeMap[$this->type] ?? static::$_unknowText;
        }
    
        /**
         * return array
         */
        public function columnMap ()
        {
            return [
                'id' => 'id',
                'suggestionId' => 'suggestionId',
                'title' => 'title',
                'type' => 'type',
                'content' => 'content',
                'assistantId' => 'assistantId',
                'hrefUrl' => 'hrefUrl',
                'isRead' => 'isRead',
                'status' => 'status',
                'workName' => 'workName',
                'gmtReaded' => 'gmtReaded',
                'gmtCreated' => 'gmtCreated',
                'gmtUpdated' => 'gmtUpdated'
            ];
        }
    }
```

##### v1.1版本
1. 已知bug修复
1. 支持多model生成，baseModel与normalModel拆分，支持重写
1. 支持数据字段注解，支持@enum(1=a|2=b)的文档显示
1. 支持单model重写 --model all
                 --model  tableName


