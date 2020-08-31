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
 * Class SingleApiWithoutModelMode
 * @package Uniondrug\Builder\Modes
 */
class SingleApiWithoutModelMode extends Mode
{
    /**
     * SingleApiWithoutModelMode constructor.
     * @param array $parameter
     */
    public function __construct(array $parameter)
    {
        parent::__construct($parameter, []);
    }

    /**
     * @throws \ReflectionException
     */
    public function run()
    {
        // 创建控制器
        $controller = new BuildController($this->parameter);
        $controller->build($this->columns);
        // 创建logic
        $logic = new BuildLogic($this->parameter);
        $logic->build($this->columns);
        // 创建service
        $service = new BuildService($this->parameter);
        $service->build($this->columns);
        // 创建 入参结构体
        $request = new BuildRequest($this->parameter);
        $request->build($this->columns);
        // 创建  出参结构体
        $result = new BuildResult($this->parameter);
        $result->build($this->columns);
        $this->console->info('生成结束');
    }
}