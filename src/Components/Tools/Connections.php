<?php
namespace Uniondrug\Builder\Components\Tools;

/**
 * 检查数据库连接
 * Class Connections
 * @package Uniondrug\Builder\Components\Tools
 */
class Connections
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
        if (!$configLists = $this->getConfigLists()) {
            return false;
        }
        if (!$connections = $this->getConnections($configLists)) {
            return false;
        }
        if (!$connections = $this->filterConnections($connections)) {
            return false;
        }
        $arr = explode("#", $this->inputArguments['table']);
        if (isset($connections[0]['pre'])){
            $this->inputArguments['table'] = $connections[0]['pre'].$arr[0];
        }else{
            $this->inputArguments['table'] = $arr[0];
        }
        return $this->getRealConnection($connections, $this->inputArguments['table']);
    }

    /**
     * 获取所有配置列表
     * @return array
     */
    private function getConfigLists()
    {
        try {
            $configLists = app()->getConfig();
        } catch(\Exception $exception) {
            return [];
        }
        return $configLists;
    }

    /**
     * 读取所有有效数据库配置
     * @param $configLists
     * @return array
     */
    private function getConnections($configLists)
    {
        $connections = [];
        if (!$configLists) {
            return [];
        }
        foreach ($configLists as $configListKey => $configList) {
            if (!$configList) {
                continue;
            }
            foreach ($configList as $configKey => $config) {
                if (!$config) {
                    continue;
                } else {
                    if (isset($config->dbname) && isset($config->host)) {
                        $config['databaseName'] = $configListKey;
                        $config['instanceName'] = $configKey;
                        array_push($connections, $config);
                    }
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
    private function filterConnections($connections)
    {
        if (!$connections) {
            return [];
        }
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
     * @return array|bool
     */
    private function getRealConnection($connections, $table)
    {
        if (!$connections) {
            return [];
        }
        foreach ($connections as $connection) {
            $this->console->info('开始检索配置文件【'.$connection['databaseName'].'】下的数据库【'.$connection['dbname'].'】');
            $model = new Model($connection);
            // mysql连接异常
            if (!$model) {
                continue;
            }
            if ($this->checkTableExist($model->getTables(), $table)) {
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
    private function checkTableExist($tables, $table)
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