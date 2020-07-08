<?php
/**
 * Created by PhpStorm.
 * User: liyang
 * Date: 2020/7/8
 * Time: 11:55 PM
 */
namespace Uniondrug\Builder\Tools;

/**
 * 工具类：实例化各类工具
 * Class ToolBar
 * @package Uniondrug\Builder\Tools
 */
class ToolBar
{
    /**
     * @var Console
     */
    public $console;
    /**
     * @var TemplateParser
     */
    public $templateParser;
    /**
     * @var array
     */
    public $columns;
    /**
     * @var string
     */
    public $table;

    public function __construct()
    {
        $this->_console();
        $this->_templateParser();
        $this->_columns();
    }

    private function _console()
    {
        $this->console = new Console();
    }

    private function _templateParser()
    {
        $this->templateParser = new TemplateParser();
    }

    private function _columns()
    {
        $model = new Model($this->_getDbConfig());
        $this->columns = $model->getColums();
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