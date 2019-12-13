<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-06
 */
namespace Uniondrug\Builder\Commands;

use Uniondrug\Builder\Parsers\Tool\Console;
use Uniondrug\Console\Command;
use Uniondrug\Builder\Parsers\Collection;

/**
 * Class Builder
 * @package Uniondrug\Builder\Commands
 */
class Builder extends Command
{
    protected $signature = 'builder
                            {--table= : 指定数据库的表名}';
    protected $authorConfig = [
        'name' => 'developer',
        'email' => 'developer@uniondrug.cn',
        'tool' => 'Builder'
    ];
    /**
     * 命令描述
     * @var string
     */
    protected $description = '脚手架生成工具';
    public $console;

    /**
     * @inheritdoc
     */
    public function handle()
    {
        $this->console = new Console();
        $this->setAuthorConfig();
        $dbConfig = $this->checkDatabase();
        $dbConfig['table'] = $this->checkArgvs();
        $collection = new Collection(getcwd(), $dbConfig, $this->authorConfig);
        $collection->build();
    }

    private function setAuthorConfig()
    {
        $nameShell = 'git config --get user.name ';
        $emailShell = 'git config --get user.email';
        $name = shell_exec($nameShell);
        $email = shell_exec($emailShell);
        if ($name) {
            $this->authorConfig['name'] = str_replace(PHP_EOL, '', $name);
        }
        if ($email) {
            $this->authorConfig['email'] = str_replace(PHP_EOL, '', $email);;
        }
    }

    /**
     * 检查数据库配置
     * @return array
     */
    private function checkDatabase()
    {
        $connection = app()->getConfig()->database->connection;
        // 检查数据库链接是否存在
        if (empty($connection)) {
            $this->console->error('config/database is not exist, please checkout your config files!');
            exit;
        }
        // 检查数据库配置是否存在
        foreach ($connection as $key => $value) {
            if (empty($value) && in_array($key, [
                    'host',
                    'username',
                    'port',
                    'password',
                    'dbname'
                ])) {
                $this->console->error(' do not have right value');
                exit;
            }
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
            $this->console->error('database table name is not exist,try again like this :');
            $this->console->error(' php console builder --table tableName');
            exit;
        }
        return $table;
    }
}
