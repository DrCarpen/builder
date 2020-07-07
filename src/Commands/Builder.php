<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-06
 */
namespace Uniondrug\Builder\Commands;

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
                            {--api= : 接口名称，默认支持接口，c(create)|d(detail)|u(update)|l(listing)|p(paging)}
                            ';
    protected $authorConfig = [];
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
        $this->console = new Console();
        $this->getAuthorInfo();
        $dbConfig['table'] = $this->checkArgvs();
        $dbConfig = $this->checkDatabase();
        // TODO::模式分发
        // 1 简单模式
        // 2 单接口模式
        $collection = new Collection(getcwd(), $dbConfig, $this->authorConfig);
        $collection->build();
    }

    /**
     * 获取用户名称信息
     */
    private function getAuthorInfo()
    {
        $nameShell = 'git config --get user.name ';
        $emailShell = 'git config --get user.email';
        $name = shell_exec($nameShell);
        $email = shell_exec($emailShell);
        if ($name) {
            $this->authorConfig['name'] = str_replace(PHP_EOL, '', $name);
        } else {
            $this->authorConfig['name'] = 'developer';
        }
        if ($email) {
            $this->authorConfig['email'] = str_replace(PHP_EOL, '', $email);
        } else {
            $this->authorConfig['email'] = 'developer@uniondrug.cn';
        }
        $this->authorConfig['tool'] = 'Builder';
    }

    /**
     * 检查数据库配置
     * @return array
     */
    private function checkDatabase()
    {
        $connection = app()->getConfig()->database->connection;
        // 检查数据库链接是否存在
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
        return [
            'host' => $connection->host,
            'user' => $connection->username,
            'password' => $connection->password,
            'database' => $connection->dbname,
            'port' => $connection->port,
            'noShowFields' => []
        ];
    }

    /**
     * 处理入参
     */
    private function checkArgvs()
    {
        $table = $this->input->getOption('table');
        if (empty($table)) {
            $this->console->errorExit('数据表名不存在，检查参数，例如下 :
                                    php console builder --table tableName');
        }
        return $table;
    }
}
