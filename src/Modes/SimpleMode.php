<?php
/**
 * Created by PhpStorm.
 * User: liyang
 * Date: 2020/7/7
 * Time: 4:29 PM
 */
namespace Uniondrug\Builder\Modes;

use Uniondrug\Builder\Components\Build\BuildModel;

/**
 * 简单模式
 * Class SimpleMode
 * @package Uniondrug\Builder\Modes
 */
class SimpleMode extends Mode
{
    public function __construct($parameter)
    {
        $this->table = $parameter['table'];
        parent::__construct();
    }

    public function run($parameter)
    {
        // 调用组件-创建model文件
        $build = new BuildModel($this->columns, $parameter);
        $build->build();
    }
}