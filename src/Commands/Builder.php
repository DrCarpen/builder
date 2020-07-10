<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-06
 */
namespace Uniondrug\Builder\Commands;

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
    protected $signature = 'builder
                            {--table= : 数据表名}
                            {--api= : 接口名称，支持自定义接口}
                            ';
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
        $this->_checkParameter();
        $this->_checkDatabase();
        $parameter = $this->_getParameter();
        // TODO::模式分发
        if ($parameter['api']) {
            $mode = new SingleApiMode($parameter);
        } else {
            $mode = new SimpleMode($parameter);
        }
        $mode->run();
    }

    private function _console()
    {
        $this->console = new Console();
    }

    /**
     * 检查数据库配置
     * @return bool
     */
    private function _checkDatabase()
    {
        $connection = app()->getConfig()->database->connection;
        if (empty(app()->getConfig()->database)) {
            $this->console->errorExit('目录文件/config/database.php 不存在，请检查目录');
        }
        if (empty(app()->getConfig()->database->connection)) {
            $this->console->errorExit('database.php的connection配置 不存在，请检查目录');
        }
        if (empty($connection->host)) {
            $this->console->errorExit('database.php的host配置 不存在，请检查目录');
        }
        if (empty($connection->port)) {
            $this->console->errorExit('database.php的port配置 不存在，请检查目录');
        }
        if (empty($connection->username)) {
            $this->console->errorExit('database.php的username配置 不存在，请检查目录');
        }
        if (empty($connection->password)) {
            $this->console->errorExit('database.php的password配置 不存在，请检查目录');
        }
        return true;
    }

    /**
     * 校验参数
     * @return bool
     */
    private function _checkParameter()
    {
        $table = $this->input->getOption('table');
        if (empty($table)) {
            $this->console->errorExit('数据表名不存在，检查参数，例如下 :
                                    php console builder --table tableName');
        }
        return true;
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
