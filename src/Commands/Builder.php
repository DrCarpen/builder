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
                            {--t : 发布markdown文档}';

    /**
     * @inheritdoc
     */
    public function handle()
    {
        $dbConfig = $this->checkDatabase();
        $this->checkArgvs();
        $this->dbConfig['table'] = $this->getArgvs() ? $this->getArgvs() : $this->dbConfig['table'];
        $collection = new Collection(getcwd(), $dbConfig, []);
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
        $argvs = $_SERVER['argv'];
        print_r( $this->getOptions());die;
        $this->argument();
        print_r($argv);die;
        if (count($argvs) > 1) {
            return $argvs[1];
        }
    }
}
