<?php
/**
 * Created by PhpStorm.
 * User: liyang
 * Date: 2020/7/9
 * Time: 12:06 AM
 */
namespace Uniondrug\Builder\Components\Modes;

use Uniondrug\Builder\Components\Build\BuildController;
use Uniondrug\Builder\Components\Build\BuildLogic;
use Uniondrug\Builder\Components\Build\BuildModel;
use Uniondrug\Builder\Components\Build\BuildRequest;
use Uniondrug\Builder\Components\Build\BuildResult;
use Uniondrug\Builder\Components\Build\BuildService;
use Uniondrug\Builder\Components\Build\BuildTrait;
use Uniondrug\Builder\Components\Tools\Console;
use Uniondrug\Builder\Components\Tools\Model;

/**
 * Class Mode
 * @package Uniondrug\Builder\Components\Modes
 */
class Mode
{
    /**
     * 数据表字段
     * @var array
     */
    public $columns;
    /**
     * @var Console
     */
    public $console;
    /**
     * @var string
     */
    public $table;
    /**
     * @var array
     */
    public $parameter;
    /**
     * @var array
     */
    protected $dbConfig;

    public function __construct(array $parameter, $dbConfig)
    {
        $this->console = new Console();
        // 初始化数据库配置
        $this->dbConfig = $dbConfig;
        // 初始化全局变量
        $this->setParameter($parameter);
        // 获取数据库的字段
        $this->filterColumns();
        $this->setColumns();
    }

    /**
     * 简单模式
     */
    public function simpleMode()
    {
        if (!$this->dbConfig) {
            $this->console->errorExit('当前数据库中无此数据表【'.$this->parameter['table'].'】，不能生成model文件');
        }
        // 创建model文件
        $build = new BuildModel($this->parameter);
        $build->build($this->columns);
        $this->console->info('生成结束');
    }

    /**
     * 单接口
     */
    public function singleApiMode()
    {
        // 创建model
        $model = new BuildModel($this->parameter);
        $model->build($this->columns);
        // 创建控制器
        $controller = new BuildController($this->parameter);
        $controller->build($this->columns);
        // 创建logic
        $logic = new BuildLogic($this->parameter);
        $logic->build($this->columns);
        // 创建 trait
        $trait = new BuildTrait($this->parameter);
        $trait->build($this->columns);
        // 创建 入参结构体
        $request = new BuildRequest($this->parameter);
        $request->build($this->columns);
        // 创建  出参结构体
        $result = new BuildResult($this->parameter);
        $result->build($this->columns);
        // 创建service
        $service = new BuildService($this->parameter);
        $service->build($this->columns);
        $this->console->info('生成结束');
    }

    /**
     * 单接口无DB
     */
    public function singleApiWithoutDBMode()
    {
        // 创建控制器
        $controller = new BuildController($this->parameter);
        $controller->build($this->columns);
        // 创建logic
        $logic = new BuildLogic($this->parameter);
        $logic->build($this->columns);
        // 创建 入参结构体
        $request = new BuildRequest($this->parameter);
        $request->build($this->columns);
        // 创建  出参结构体
        $result = new BuildResult($this->parameter);
        $result->build($this->columns);
        // 创建service
        $service = new BuildService($this->parameter);
        $service->build($this->columns);
        $this->console->info('生成结束');
    }

    /**
     * 配置参数
     * @param $parameter
     */
    protected function setParameter($parameter)
    {
        $this->parameter = $parameter;
        $this->table = key_exists('table', $parameter) ? $parameter['table'] : '';
    }

    /**
     * @return bool
     */
    private function filterColumns()
    {
        if (!$this->dbConfig) {
            return false;
        }
        $model = new Model($this->dbConfig);
        $columns = $model->getColumns();
        foreach ($columns as $columnKey => $column) {
            $columns[$columnKey][columnComment] = preg_replace('/\\n/', '', $column['columnComment']);
        }
        $this->columns = $columns;
    }

    /**
     * 获取表字段
     * @return bool
     */
    private function setColumns()
    {
        $columns = $this->columns;
        if (!$columns) {
            return true;
        }
        foreach ($columns as $columnKey => $column) {
            $columns[$columnKey]['camelColumnName'] = $this->getLowerCamelCase($column['columnName']);
            $columns[$columnKey][columnComment] = preg_replace('/\\n/', '', $column['columnComment']);
            $columns[$columnKey]['underlineColumnName'] = $this->getUnderlineCase($column['columnName']);
            $columns[$columnKey]['sitAnnotation'] = $this->getAnnotation($column['columnComment']);
        }
        $this->columns = $columns;
        return true;
    }

    /**
     * 获取小驼峰字段
     * @param      $str
     * @param bool $ucfirst
     * @return mixed|string
     */
    private function getLowerCamelCase($str, $ucfirst = false)
    {
        $str = ucwords(str_replace('_', ' ', $str));
        $str = str_replace(' ', '', lcfirst($str));
        return $ucfirst ? ucfirst($str) : $str;
    }

    /**
     * @param        $camelCaps
     * @param string $separator
     * @return string
     */
    private function getUnderlineCase($camelCaps, $separator = '_')
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', "$1".$separator."$2", $camelCaps));
    }

    /**
     * 解析注释内容
     * @param $columnComment
     * @return array
     */
    private function getAnnotation($columnComment)
    {
        $pregRule = "/^(\=|\:|\：|\||\,|\，|\-|\(|\)|\（|\）)*|(\=|\:|\：|\||\,|\，|\-|\(|\)|\（|\）)*$/";
        // 匹配是否有数字
        preg_match_all('/-?\d+/u', $columnComment, $match);
        if (!$match[0]) {
            return [
                'main' => $columnComment,
                'sit' => []
            ];
        }
        // 切分数组
        $commentChips = preg_split('/-?\d+/u', $columnComment);
        // 主注释
        $annotation['main'] = preg_replace($pregRule, '', $commentChips[0]);
        unset($commentChips[0]);
        sort($commentChips);
        // 分解注释
        $annotation['sit'] = [];
        $max = count($commentChips) > count($match[0]) ? count($commentChips) : count($match[0]);
        for ($i = 0; $i < $max; $i++) {
            if (!key_exists($i, $commentChips) || !key_exists($i, $match[0])) {
                break;
            }
            $annotation['sit'][] = [
                'sitStatus' => $match[0][$i],
                'sitComment' => preg_replace($pregRule, '', trim($commentChips[$i])),
            ];
        }
        return $annotation;
    }
}