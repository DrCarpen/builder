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
use Phalcon\Config;

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

    public function __construct(array $parameter, Config $dbConfig)
    {
        // 初始化数据库配置
        $this->dbConfig = $dbConfig;
        $this->_console();
        // 初始化全局变量
        $this->_parameter($parameter);
        // 获取数据库的字段
        $this->_columns();
    }

    protected function _parameter($parameter)
    {
        $this->parameter = $parameter;
        $this->table = key_exists('table', $parameter) ? $parameter['table'] : '';
    }

    private function _console()
    {
        $this->console = new Console();
    }

    private function _columns()
    {
        $model = new Model($this->_getDbConfig());
        $columns = $model->getColums();
        $_columns = [];
        foreach ($columns as $column) {
            $_columns[] = [
                'columnName' => $column['COLUMN_NAME'],
                'columnDefault' => $column['COLUMN_DEFAULT'],
                'isNullAble' => $column['IS_NULLABLE'],
                'dataType' => $column['DATA_TYPE'],
                'characterMaximumLength' => $column['CHARACTER_MAXIMUM_LENGTH'],
                'numericPrecision' => $column['NUMERIC_PRECISION'],
                'columnComment' => $column['COLUMN_COMMENT'],
                'columnKey' => $column['COLUMN_KEY']
            ];
        }
        $this->columns = $_columns;
    }

    private function _getDbConfig()
    {
        $dbConfig['host'] = $this->dbConfig['host'];
        $dbConfig['username'] = $this->dbConfig['username'];
        $dbConfig['password'] = $this->dbConfig['password'];
        $dbConfig['dbname'] = $this->dbConfig['dbname'];
        $dbConfig['port'] = $this->dbConfig['port'];
        $dbConfig['table'] = $this->table;
        return $dbConfig;
    }
}