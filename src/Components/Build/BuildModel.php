<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-06
 */
namespace Uniondrug\Builder\Components\Build;

use Uniondrug\Builder\Components\Build\BuildBasic;

class BuildModel extends BuildBasic
{
    public function __construct($columns, $parameter)
    {
        parent::__construct($parameter);
    }

    public function build()
    {
        // 获取文件名称
        $className = $this->getClassName('Model');

        $propertyTemplateList = '';
        echo $this->getAuthorContent();
        die;
        // 判断目录是否存在
        // 创建参数
        // 获取模板
        // 注入模板
        // 生成文件
    }
}