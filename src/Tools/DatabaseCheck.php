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
    private $console;
    private $singleDB = [
        'database',
        'db'
    ];
    private $multiplyDB = [
        'databases',
        'dbs'
    ];
    /**
     * 数据库配置必填项
     * @var string[]
     */
    protected $_dbConfigItemRequired = [
        'host',
        'dbname',
        'username',
        'password'
    ];

    public function __construct($inputArguments)
    {
        $this->console = new Console();
        if (!$connections = $this->getConnections()) {
            return false;
        }
        if (!$connections = $this->checkConnections($connections)) {
            return false;
        }
        return $this->getRealConnection($connections, $inputArguments['table']);
    }

    /**
     * @return array
     */
    private function getConnections()
    {
        $connections = [];
        foreach ($this->singleDB as $single) {
            if ($parts = app()->getConfig()->{$single}) {
                foreach ($parts as $partKey => $part) {
                    $part['dbSource'] = $single.'->'.$partKey;
                    array_push($connections, $part);
                }
            }
        }
        foreach ($this->multiplyDB as $multiply) {
            if ($parts = app()->getConfig()->{$multiply}) {
                foreach ($parts as $partKey => $part) {
                    $part['dbSource'] = $multiply.'->'.$partKey;
                    array_push($connections, $part);
                }
            }
        }
        return $connections;
    }

    /**
     * @param $connections
     * @return mixed
     */
    private function checkConnections($connections)
    {
        foreach ($connections as $connectionKey => $connection) {
            foreach ($this->_dbConfigItemRequired as $item) {
                if (!$connection[$item]) {
                    $this->console->warning($connection['dbSource'].'下的【'.$item.'】项配置缺失，已剔除');
                    unset($connections[$connectionKey]);
                    continue;
                }
            }
        }
        return $connections;
    }

    /**
     * @param $connections
     * @param $table
     * @return bool
     */
    private function getRealConnection($connections, $table)
    {
        foreach ($connections as $connection) {
            $model = new Model($connection);
            // mysql连接异常
            if (!$model) {
                continue;
            }
            if ($this->checkTableExist($model->getTables(), $table)) {
                return $connection;
            }
        }
//        $this->console->warning()
        return false;
    }

    /**
     * @param $tables
     * @param $table
     * @return bool
     */
    private function checkTableExist($tables, $table)
    {
        $isExist = false;
        foreach ($tables as $item) {
            if ($item == $table) {
                $isExist = true;
            }
        }
        return $isExist;
    }
}