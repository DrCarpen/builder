<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-06
 */
namespace Uniondrug\Builder\Commands;

use Phalcon\Config;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputAwareInterface;
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
            {--table=|-t : Table\'s name}
            {--database=|-d : Database\'s name [databases.example_db]}
            {--api= : 接口名称，支持自定义接口}';
    /**
     * 命令描述
     * @var string
     */
    protected $description = '脚手架生成工具';
    /**
     * @var Console
     */
    public $console;

    /**
     * @inheritdoc
     */
    public function handle()
    {
        $this->_console();
        $dbConfig = $this->_checkDatabase();
        $parameter = $this->_getParameter();
        // TODO::模式分发
        if ($parameter['api']) {
            $mode = new SingleApiMode($parameter, $dbConfig);
        } else {
            $mode = new SimpleMode($parameter, $dbConfig);
        }
        $mode->run();
    }

    private function _console()
    {
        $this->console = new Console();
    }

    /**
     * 获取默认的数据库连接
     * @return mixed|Config
     */
    public function getDefaultDatabase()
    {
        $defaultDbFile = 'database.php';
        $filePath = \app()->configPath().'/'.$defaultDbFile;
        if (!$filePath) {
            throw new RuntimeException('The file of '.$defaultDbFile.' not exist !');
        }
        // 配置文件的数据库配置不存在
        $defaultDbConfig = \config()->path('database.connection');
        if (!$defaultDbConfig) {
            throw new RuntimeException('The connection of '.$defaultDbFile.' not exist!');
        }
        return \config()->path('database.connection');
    }

    /**
     * 解析输入数据库参数
     * @return Config
     */
    public function parseDatabase()
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
     * 解析输入表参数
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
    }

    /**
     * 检查数据库配置
     * @return Config
     */
    private function _checkDatabase()
    {
        // 解析输入数据库参数
        $dbConfig = $this->parseDatabase();
        // 解析输入表参数
        $table = $this->parseTable();
        $dbConfig = $dbConfig ?: $this->getDefaultDatabase();
        foreach ($dbConfig as $key => $config) {
            if (empty($config)) {
                throw new RuntimeException('The config \''.$config.'\' of database is required');
            }
        }
        return $dbConfig;
    }

    /**
     * 参数
     * @return array
     */
    private function _getParameter()
    {
        return $this->input->getOptions();
    }
}
