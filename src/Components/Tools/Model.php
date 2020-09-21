<?php
namespace Uniondrug\Builder\Components\Tools;

/**
 * Class Model
 * @package Uniondrug\Builder\Components\Tools
 */
class Model
{
    private $table;
    private $host;
    private $username;
    private $password;
    private $dbname;
    private $port;
    private $charset;
    private $connection;
    private $console;

    /**
     * Model constructor.
     * @param $dbConfig
     */
    public function __construct($dbConfig)
    {
        $this->console = new Console();
        $this->_setConfig($dbConfig);
        // 初始化数据库连接
        if (!$this->_connect()) {
            return false;
        }
        // 设置数据名
        $this->_setDb();
        // 设置字符集
        $this->_setCharset();
        return true;
    }

    /**
     * 读取所有表
     * @return array
     */
    public function getTables()
    {
        return $this->_queryAll('show tables');
    }

    /**
     * 获取表的字段信息
     * @return mixed
     */
    public function getColumns()
    {
        $sql = 'SELECT ';
        $sql .= '`COLUMN_NAME` as `columnName`,';
        $sql .= '`COLUMN_DEFAULT` as `columnDefault`,';
        $sql .= '`IS_NULLABLE` as `isNullAble`,';
        $sql .= '`DATA_TYPE` as `dataType`,';
        $sql .= '`CHARACTER_MAXIMUM_LENGTH` as `characterMaximumLength`,';
        $sql .= '`NUMERIC_PRECISION` as `numericPrecision`,';
        $sql .= '`COLUMN_COMMENT` as `columnComment`,';
        $sql .= '`COLUMN_KEY` as `columnKey`';
        $sql .= 'FROM INFORMATION_SCHEMA.COLUMNS ';
        $sql .= 'WHERE ';
        $sql .= "table_name = '{$this->table}' AND table_schema = '{$this->dbname}' ";
        $sql .= "ORDER BY ORDINAL_POSITION";
        return $this->_queryAll($sql);
    }

    /**
     * 初始化配置信息
     * @param $dbConfig
     */
    private function _setConfig($dbConfig)
    {
        $this->host = $dbConfig['host'];
        $this->username = $dbConfig['username'];
        $this->password = $dbConfig['password'];
        $this->dbname = $dbConfig['dbname'];
        $this->table = $dbConfig['table'];
        $this->port = $dbConfig['port'] ? $dbConfig['port'] : 3712;
        $this->charset = $dbConfig['charset'] ? $dbConfig['charset'] : 'utf8';
    }

    /**
     * 连接数据库
     * @return bool
     */
    private function _connect()
    {
        $connection = mysqli_connect("$this->host", "$this->username", "$this->password", "$this->dbname", "$this->port");
        if (!$connection) {
            $this->console->error('连接MySQL服务器失败,错误码:'.mysqli_connect_errno().'错误提示:'.mysqli_connect_error());
            return false;
        }
        $this->connection = $connection;
    }

    /**
     * 设置数据库名
     */
    private function _setDb()
    {
        mysqli_select_db($this->connection, $this->dbname);
    }

    /**
     * 查询
     * @param $sql
     * @return bool|\mysqli_result
     */
    private function _query($sql)
    {
        return mysqli_query($this->connection, $sql);
    }

    /**
     * 设置字符集
     */
    private function _setCharset()
    {
        $this->_query('SET NAMES '.$this->charset);
    }

    /**
     * @param $sql
     * @return array
     */
    private function _queryAll($sql)
    {
        $res = mysqli_query($this->connection, $sql);
        $result = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $result[] = $row;
        }
        return $result;
    }
}