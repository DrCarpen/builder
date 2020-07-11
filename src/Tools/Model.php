<?php
namespace Uniondrug\Builder\Tools;

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
    private $connection;
    private $console;

    public function __construct($dbConfig)
    {
        // 获取console
        $this->_console();
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

    private function _console()
    {
        $this->console = new Console();
    }

    private function _config($dbConfig)
    {
        $this->host = $dbConfig['host'];
        $this->username = $dbConfig['username'];
        $this->password = $dbConfig['password'];
        $this->dbname = $dbConfig['dbname'];
        $this->port = $dbConfig['port'];
        $this->table = $dbConfig['table'];
    }

    /**
     * 连接数据库，获取句柄
     */
    private function _connect()
    {
        $connection = mysqli_connect("$this->host", "$this->username", "$this->password", "$this->dbname", "$this->port");
        if (!$connection) {
            $this->console->error('无法连接上MySQL服务器');
            $this->console->error('错误码：'.mysqli_connect_errno());
            $this->console->errorExit('错误提示：'.mysqli_connect_error());
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
        $this->_query('SET NAMES utf8');
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
            $this->console->errorExit('此表['.$this->table.']不存在于数据库 ['.$this->dbname.']中，请检查数据库及配置！');
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