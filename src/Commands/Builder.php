<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-06
 */
namespace Uniondrug\Builder\Commands;

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
        'name' => 'dev',
        'email' => 'dev@uniondrug.com'
    ];
    /**
     * 命令描述
     * @var string
     */
    protected $description = '脚手架生成工具';

    /**
     * @inheritdoc
     */
    public function handle()
    {
        $dbConfig = $this->checkDatabase();
        $dbConfig['table'] = $this->checkArgvs();
        $collection = new Collection(getcwd(), $dbConfig, $this->authorConfig);
        $collection->build();
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
            echo 'config/database is not exist, please checkout your config files!';
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
                echo $value.' do not have right value';
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
            echo 'database table name is not exist'.PHP_EOL;
            echo 'try again like this :'.PHP_EOL;
            echo ' php console builder --t tableName'.PHP_EOL;
            exit;
        }
        return $table;
    }
}
