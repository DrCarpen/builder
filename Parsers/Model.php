<?php

/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-06
 */
class Model
{
    private $table;
    private $host;
    private $userName;
    private $dbPwd;
    private $database;
    private $port;
    private $conn;

    public function __construct($dbConfig)
    {
        $this->host = $dbConfig['host'];
        $this->userName = $dbConfig['user'];
        $this->dbPwd = $dbConfig['password'];
        $this->database = $dbConfig['database'];
        $this->port = $dbConfig['port'];
        $this->table = $dbConfig['table'];
        // 初始化数据库连接
        $this->connect();
        // 设置数据名
        $this->setDb();
        // 设置字符集
        $this->setCharset();
    }

    /**
     * @return mixed
     */
    public function build()
    {
        $this->checkTable();
        return $this->getColumns();
    }

    /**
     * 连接数据库，获取句柄
     */
    private function connect()
    {
        $conn = mysqli_connect("$this->host:".$this->port, "$this->userName", "$this->dbPwd");
        if (!$conn) {
            echo "Error: Unable to connect to MySQL.".PHP_EOL;
            echo "Debugging errno: ".mysqli_connect_errno().PHP_EOL;
            echo "Debugging error: ".mysqli_connect_error().PHP_EOL;
            exit;
        }
        $this->conn = $conn;
    }

    /**
     * 设置数据库名
     */
    private function setDb()
    {
        mysqli_select_db($this->conn, $this->database);
    }

    /**
     * 设置字符集
     */
    private function setCharset()
    {
        $this->query('SET NAMES utf8');
    }

    /**
     * 查询
     * @param $sql
     * @return bool|mysqli_result
     */
    private function query($sql)
    {
        return mysqli_query($this->conn, $sql);
    }

    /**
     * 断点测试
     * @param $data
     */
    private function p($data)
    {
        echo '<pre>';
        print_r($data);
        echo '<br>';
    }

    /**
     * @param $sql
     * @return array
     */
    public function queryAll($sql)
    {
        $res = mysqli_query($this->conn, $sql);
        $result = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $result[] = $row;
        }
        return $result;
    }

    /**
     * 筛选表
     */
    private function checkTable()
    {
        $tables = $this->queryAll('show tables');
        $flag = false;
        foreach ($tables as $val) {
            if ($this->table == $val['Tables_in_'.$this->database]) {
                $flag = true;
            }
        }
        if ($flag == false) {
            echo 'There is no table in this database,please check in the config';
            die;
        }
    }

    /**
     * 获取表的字段信息
     * @return mixed
     */
    private function getColumns()
    {
        $sql = 'SELECT * FROM ';
        $sql .= 'INFORMATION_SCHEMA.COLUMNS ';
        $sql .= 'WHERE ';
        $sql .= "table_name = '{$this->table}' AND table_schema = '{$this->database}'";
        return $this->queryAll($sql);
    }
}