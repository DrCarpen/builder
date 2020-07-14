<?php
/**
 * Created by PhpStorm.
 * User: liyang
 * Date: 2020/7/7
 * Time: 4:29 PM
 */
namespace Uniondrug\Builder\Modes;

use Uniondrug\Builder\Components\Build\BuildModel;
use Phalcon\Config;

/**
 * 简单模式
 * Class SimpleMode
 * @package Uniondrug\Builder\Modes
 */
class SimpleMode extends Mode
{
    public function __construct(array $parameter, Config $dbConfig)
    {
        parent::__construct($parameter, $dbConfig);
    }

    public function run()
    {
        // 调用组件-创建model文件
        $build = new BuildModel($this->parameter);
        $build->build($this->columns);
    }
}