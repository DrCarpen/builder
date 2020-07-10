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
     * @var string
     */
    public $api;

    public function __construct()
    {
        $this->_console();
        // 获取数据库的字段
        $this->_columns();
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
                'numbericPrecision' => $column['NUMERIC_PRECISION'],
                'columnComment' => $column['COLUMN_COMMENT'],
                'columnKey' => $column['COLUMN_KEY']
            ];
        }
        $this->columns = $_columns;
    }

    private function _getDbConfig()
    {
        $connection = app()->getConfig()->database->connection;
        $dbConfig['host'] = $connection['host'];
        $dbConfig['username'] = $connection['username'];
        $dbConfig['password'] = $connection['password'];
        $dbConfig['dbname'] = $connection['dbname'];
        $dbConfig['port'] = $connection['port'];
        $dbConfig['table'] = $this->table;
        return $dbConfig;
    }
}