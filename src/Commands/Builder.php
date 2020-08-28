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
use Uniondrug\Builder\Modes\SingleApiWithoutModelMode;
use Uniondrug\Builder\Tools\DatabaseCheck;
use Uniondrug\Builder\Tools\Model;
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
            {--table=|-t : 表名，或控制器名}
            {--api=|-a : 接口名称，支持自定义接口}';
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
        $dbConfig = $this->_getDatabase($parameter);
        // TODO::模式分发
        if (!$parameter['api']) {
            $mode = new SimpleMode($parameter, $dbConfig);
        } else if ($dbConfig) {
            $mode = new SingleApiMode($parameter, $dbConfig);
        } else {
            $mode = new SingleApiWithoutModelMode($parameter);
        }
        $mode->run();
    }

    /**
     * 读取数据库配置
     * @param $parameter
     * @return bool|array
     */
    private function _getDatabase($parameter)
    {
        $databaseCheck = new DatabaseCheck($parameter);
        return $databaseCheck->getConnection();
    }

    /**
     * 读取输入的命令
     * @return array
     */
    public function getInputArguments()
    {
        return $this->input->getOptions();
    }

    private function askQuestion()
    {
        $fh = fopen('php://stdin', 'r');
        echo "请输入任意字符";
        $str = fread($fh, 1000);
    }
}
