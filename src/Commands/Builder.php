<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-06
 */
namespace Uniondrug\Builder\Commands;

use Phalcon\Config;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputAwareInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Uniondrug\Builder\Modes\SimpleMode;
use Uniondrug\Builder\Modes\SingleApiMode;
use Uniondrug\Console\Command;
use Uniondrug\Builder\Parsers\Collection;
use Uniondrug\Builder\Tools\Console;

/**
 * Class Builder
 * @package Uniondrug\Builder\Commands
 */
class Builder extends Command
{
    /**
     * @var string
     */
    protected $signature = 'builder
            {--database=|-d : Database\'s name [databases.example_db]}
            {--table=|-t : Table\'s name}
            {--column=|-c : Table\'s column name}
            {--api= : 接口名称，支持自定义接口}';
    /**
     * 命令描述
     * @var string
     */
    protected $description = '脚手架生成工具';
    /**
     * 数据库配置必填项
     * @var string[]
     */
    protected $_dbConfigItemRequired = [
        'adapter',
        'host',
        'port',
        'dbname',
        'charset',
        'username',
        'password'
    ];

    /**
     * @inheritdoc
     */
    public function handle()
    {
        $parameter = $this->getInputArguments();
        $dbConfig = $this->_checkDatabase();
        // TODO::模式分发
        if ($parameter['api']) {
            $mode = new SingleApiMode($parameter, $dbConfig);
        } else {
            $mode = new SimpleMode($parameter, $dbConfig);
        }
        $mode->run();
    }

    /**
     * 获取默认的数据库连接
     * @return Config
     */
    public function getDefaultDatabase()
    {
        $defaultDbFile = 'database.php';
        $filePath = \app()->configPath().'/'.$defaultDbFile;
        if (!$filePath) {
            throw new RuntimeException('The file of '.$defaultDbFile.' not exist');
        }
        // 配置文件的数据库配置不存在
        $defaultDbConfig = \config()->path('database.connection');
        if (!$defaultDbConfig) {
            throw new RuntimeException('The connection of '.$defaultDbFile.' is required');
        }
        return $defaultDbConfig;
    }

    /**
     * 解析输入参数并返回数据库
     * @return Config
     */
    public function parseInputArgv()
    {
        // 参数databases.example_db
        $inputDbStr = $this->input->getOption('database');
        if ($inputDbStr === null) {
            return;
        }
        if (empty($inputDbStr)) {
            throw new RuntimeException('The option of --database value is required');
        }
        $inputDbArr = explode('.', $inputDbStr);
        $filePath = \app()->configPath().'/'.current($inputDbArr).'.php';
        // 格式不正确
        if (count($inputDbArr) != 2) {
            throw new RuntimeException('The option of --database format must be databases.example_db');
        }
        // 配置文件不存在
        if (!file_exists($filePath)) {
            throw new RuntimeException('The file of '.current($inputDbArr).' not exist !');
        }
        // 配置文件的数据库配置不存在
        $dbConfig = \config()->path($inputDbStr);
        if (!$dbConfig) {
            throw new RuntimeException('The config '.next($inputDbArr).' not exist!');
        }
        return $dbConfig;
    }

    /**
     * 解析输入表参数并返回表名
     * @return bool|string
     */
    public function parseTable()
    {
        $inputTable = $this->input->getOption('table');
        if (!isset($inputTable)) {
            throw new RuntimeException('The option of --table is required');
        }
        if (empty($inputTable)) {
            throw new RuntimeException('The option of --table value is required');
        }
        return $inputTable;
    }

    /**
     * 检查数据库配置
     * @return Config
     */
    private function _checkDatabase()
    {
        // 解析输入数据库参数
        $dbConfig = $this->parseInputArgv();
        // 获取默认的数据库配置
        $dbConfig = $dbConfig ?: $this->getDefaultDatabase();
        $dbConfigItem = array_keys((array) $dbConfig);
        // 缺失的必填项
        $_lackRequired = array_diff($this->_dbConfigItemRequired, $dbConfigItem);
        foreach ($_lackRequired as $vconfig) {
            throw new RuntimeException('The config \''.$vconfig.'\' of database is required');
        }
        foreach ($dbConfig as $kconfig => $vconfig) {
            if (empty($vconfig)) {
                throw new RuntimeException('The config \''.$kconfig.'\' value of database is required');
            }
        }
        // 解析输入表参数并合并到数据库配置
        $dbConfig->table = $this->parseTable();
        return $dbConfig;
    }

    /**
     * @return array
     */
    public function getInputArguments()
    {
        return $this->input->getOptions();
    }
}
