<?php
/**
 * Created by PhpStorm.
 * User: liyang
 * Date: 2020/7/7
 * Time: 4:29 PM
 */
namespace Uniondrug\Builder\Modes;

/**
 * 单接口模式
 * Class SingleApiMode
 * @package Uniondrug\Builder\Modes
 */
class SingleApiMode extends Mode
{
    public $dbConfig;
    public $authorConfig;
    public $base;

    public function __construct($base, $dbConfig, $authorConfig)
    {
        parent::__construct();
        $this->base = $base;
        $this->dbConfig = $dbConfig;
        $this->authorConfig = $authorConfig;
    }

    public function run($api, $model)
    {
        // 创建model
        // 创建控制器
        // 创建logic
        // 创建service
        // 创建 trait
        // 创建 入参结构体
        // 创建  出参结构体
    }
}