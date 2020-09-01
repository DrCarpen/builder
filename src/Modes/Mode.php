<?php
/**
 * Created by PhpStorm.
 * User: liyang
 * Date: 2020/7/9
 * Time: 12:06 AM
 */
namespace Uniondrug\Builder\Modes;

use Uniondrug\Builder\Tools\Console;
use Uniondrug\Builder\Tools\Model;

/**
 * Class Mode
 * @package Uniondrug\Builder\Modes
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
        $this->_setParameter($parameter);
        // 获取数据库的字段
        if ($dbConfig) {
            $this->_getColumns();
        }
    }

    /**
     * 配置参数
     * @param $parameter
     */
    protected function _setParameter($parameter)
    {
        $this->parameter = $parameter;
        $this->table = key_exists('table', $parameter) ? $parameter['table'] : '';
    }

    /**
     * 获取表字段
     */
    private function _getColumns()
    {
        $model = new Model($this->dbConfig);
        $columns = $model->getColumns();
        foreach ($columns as $columnKey => $column) {
            $columns[$columnKey]['camelColumnName'] = $this->getLowerCamelCase($column['columnName']);
            $columns[$columnKey]['underlineColumnName'] = $this->getUnderlineCase($column['columnName']);
            $columns[$columnKey]['sitAnnotation'] = $this->getAnnotation($column['columnComment']);
        }
        $this->columns = $columns;
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