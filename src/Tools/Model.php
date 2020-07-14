<?php
namespace Uniondrug\Builder\Tools;

use Phalcon\Config;
use Symfony\Component\Console\Exception\RuntimeException;
use Uniondrug\Builder\Tools\Base;

/**
 * Class Model
 * @package Uniondrug\Builder\Tools
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

    public function __construct(Config $dbConfig)
    {
        // 获取配置
        $this->_config($dbConfig);
        // 初始化数据库连接
        $this->_connect();
        // 设置数据名
        $this->_setDb();
        // 设置字符集
        $this->_setCharset();
    }

    /**
     * @return mixed
     */
    public function getColums()
    {
        $this->_checkTable();
        return $this->_getColumns();
    }

    private function _config(Config $dbConfig)
    {
        $this->host = $dbConfig['host'];
        $this->username = $dbConfig['username'];
        $this->password = $dbConfig['password'];
        $this->dbname = $dbConfig['dbname'];
        $this->port = $dbConfig['port'];
        $this->table = $dbConfig['table'];
        $this->charset = $dbConfig['charset'];
    }

    /**
     * 连接数据库，获取句柄
     */
    private function _connect()
    {
        $connection = mysqli_connect("$this->host", "$this->username", "$this->password", "$this->dbname", "$this->port");
        if (!$connection) {
            throw new RuntimeException('连接上MySQL服务器失败,错误码:'.mysqli_connect_errno().'错误提示:'.mysqli_connect_error());
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
     * 设置字符集
     */
    private function _setCharset()
    {
        $this->_query('SET NAMES '.$this->charset);
    }

    /**
     * 查询
     * @param $sql
     * @return bool|mysqli_result
     */
    private function _query($sql)
    {
        return mysqli_query($this->connection, $sql);
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

    /**
     * 筛选表
     */
    private function _checkTable()
    {
        $tables = $this->_queryAll('show tables');
        $flag = false;
        foreach ($tables as $val) {
            if ($this->table == $val['Tables_in_'.$this->dbname]) {
                $flag = true;
            }
        }
        if ($flag == false) {
            throw new RuntimeException('The database of '.$this->dbname.' has no '.$this->table);
        }
    }

    /**
     * 获取表的字段信息
     * @return mixed
     */
    private function _getColumns()
    {
        $sql = 'SELECT * FROM ';
        $sql .= 'INFORMATION_SCHEMA.COLUMNS ';
        $sql .= 'WHERE ';
        $sql .= "table_name = '{$this->table}' AND table_schema = '{$this->dbname}'";
        return $this->_queryAll($sql);
    }
}