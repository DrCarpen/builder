<?php
/**
 * Created by PhpStorm.
 * User: liyang
 * Date: 2020/7/7
 * Time: 4:29 PM
 */
namespace Uniondrug\Builder\Modes;

use Uniondrug\Builder\Components\Build\BuildController;
use Uniondrug\Builder\Components\Build\BuildLogic;
use Uniondrug\Builder\Components\Build\BuildModel;
use Uniondrug\Builder\Components\Build\BuildRequest;
use Uniondrug\Builder\Components\Build\BuildResult;
use Uniondrug\Builder\Components\Build\BuildService;
use Uniondrug\Builder\Components\Build\BuildTrait;

/**
 * 单接口模式
 * Class SingleApiMode
 * @package Uniondrug\Builder\Modes
 */
class SingleApiMode extends Mode
{
    public function __construct($parameter)
    {
        $this->table = $parameter['table'];
        $this->api = $parameter['api'];
        parent::__construct();
    }

    public function run($parameter)
    {
        // 创建model
        $model = new BuildModel($parameter);
        $model->build($this->columns);
        // 创建控制器
        $controller = new BuildController($parameter);
        $controller->build($this->columns);
        // 创建logic
        $logic = new BuildLogic($parameter);
        $logic->build($this->columns);
        // 创建service
        $service = new BuildService($parameter);
        $service->build($this->columns);
        // 创建 trait
        $trait = new BuildTrait($parameter);
        $trait->build($this->columns);
        // 创建 入参结构体
        $request = new BuildRequest($parameter);
        $request->build($this->columns);
        // 创建  出参结构体
        $result = new BuildResult($parameter);
        $result->build($this->columns);
    }
}