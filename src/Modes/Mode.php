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
     * @var Config
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
     * @param $parameter
     */
    protected function _setParameter($parameter)
    {
        $this->parameter = $parameter;
        $this->table = key_exists('table', $parameter) ? $parameter['table'] : '';
    }

    /**
     *
     */
    private function _getColumns()
    {
        $model = new Model($this->dbConfig);
        $this->columns = $model->getColumns();
    }
}