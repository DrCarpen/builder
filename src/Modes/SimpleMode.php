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
    /**
     * SimpleMode constructor.
     * @param array $parameter
     * @param       $dbConfig
     */
    public function __construct(array $parameter, $dbConfig)
    {
        parent::__construct($parameter, $dbConfig);
    }

    /**
     * @throws \ReflectionException
     */
    public function run()
    {
        if (!$this->dbConfig) {
            $this->console->errorExit('当前数据库中无此数据表【'.$this->parameter['table'].'】，不能生成model文件');
        }
        // 调用组件-创建model文件
        $build = new BuildModel($this->parameter);
        $build->build($this->columns);
        $this->console->info('生成结束');
    }
}