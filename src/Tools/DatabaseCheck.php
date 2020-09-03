<?php
namespace Uniondrug\Builder\Tools;

use Symfony\Component\Console\Exception\RuntimeException;
use Uniondrug\Builder\Tools\Base;

/**
 * Class Model
 * @package Uniondrug\Builder\Tools
 */
class DatabaseCheck
{
    /**
     * @var Console
     */
    private $console;
    /**
     * @var array
     */
    private $inputArguments;
    /**
     * 默认的数据库配置文件名
     * @var array
     */
    private $databaseNameList = [
        'database',
        'databases',
        'db',
        'dbs',
        'db1',
        'db2'
    ];
    /**
     * 数据库配置必填项
     * @var array
     */
    protected $_dbConfigItemRequired = [
        'host',
        'dbname',
        'username',
        'password'
    ];

    /**
     * DatabaseCheck constructor.
     * @param $inputArguments
     */
    public function __construct($inputArguments)
    {
        $this->console = new Console();
        $this->inputArguments = $inputArguments;
    }

    /**
     * 读取可用的数据库连接配置
     * @return bool
     */
    public function getConnection()
    {
        if (!$connections = $this->_getConnections()) {
            return false;
        }
        if (!$connections = $this->_checkConnections($connections)) {
            return false;
        }
        return $this->_getRealConnection($connections, $this->inputArguments['table']);
    }

    /**
     * 读取所有连接
     * @return array
     */
    private function _getConnections()
    {
        $connections = [];
        foreach ($this->databaseNameList as $single) {
            try {
                $parts = app()->getConfig()->{$single};
            } catch(\Exception $exception) {
                continue;
            }
            if ($parts) {
                foreach ($parts as $partKey => $part) {
                    $part['databaseName'] = $single;
                    $part['instanceName'] = $partKey;
                    array_push($connections, $part);
                }
            }
        }
        return $connections;
    }

    /**
     * 检查过滤无效连接
     * @param $connections
     * @return mixed
     */
    private function _checkConnections($connections)
    {
        foreach ($connections as $connectionKey => $connection) {
            foreach ($this->_dbConfigItemRequired as $item) {
                if (!$connection[$item]) {
                    $this->console->warning($connection['databaseName'].$connection['instanceName'].'下的【'.$item.'】项配置缺失，已剔除');
                    unset($connections[$connectionKey]);
                    continue;
                }
            }
        }
        return $connections;
    }

    /**
     * 获取真实连接
     * @param $connections
     * @param $table
     * @return bool
     */
    private function _getRealConnection($connections, $table)
    {
        foreach ($connections as $connection) {
            $this->console->info('开始检索配置文件【'.$connection['databaseName'].'】下的数据库【'.$connection['dbname'].'】');
            $model = new Model($connection);
            // mysql连接异常
            if (!$model) {
                continue;
            }
            if ($this->_checkTableExist($model->getTables(), $table)) {
                $this->console->info('选取配置文件【'.$connection['databaseName'].'】下的数据库【'.$connection['dbname'].'】');
                $connection['table'] = $table;
                return $connection;
            }
            $this->console->warning('配置文件【'.$connection['databaseName'].'】下的数据库【'.$connection['dbname'].'】无此数据表');
        }
        return false;
    }

    /**
     * @param $tables
     * @param $table
     * @return bool
     */
    private function _checkTableExist($tables, $table)
    {
        $isExist = false;
        foreach ($tables as $items) {
            foreach ($items as $item) {
                if ($item == $table) {
                    $isExist = true;
                }
            }
        }
        return $isExist;
    }
}