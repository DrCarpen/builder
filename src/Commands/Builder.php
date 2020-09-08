<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-06
 */
namespace Uniondrug\Builder\Commands;

use Uniondrug\Builder\Components\Modes\Mode;
use Uniondrug\Builder\Components\Tools\Connections;
use Uniondrug\Builder\Components\Tools\Console;
use Uniondrug\Builder\Components\Tools\DatabaseCheck;
use Uniondrug\Console\Command;
use Uniondrug\Framework\Models\Model;

/**
 * Class Builder
 * @package Uniondrug\Builder\Commands
 */
class Builder extends Command
{
    /**
     * @var Console
     */
    public $console;
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
    protected $description = 'Builder-代码生成工具';

    /**
     * @inheritdoc
     */
    public function handle()
    {
        $this->console = new Console();
        $parameter = $this->getInputArguments();
        $dbConfig = $this->getDatabase($parameter);
        $mode = new Mode($parameter, $dbConfig);
        // 模式分发
        if (!$parameter['api']) {
            $mode->simpleMode();
        } else if ($dbConfig) {
            $mode->singleApiMode();
        } else {
            $mode->singleApiWithoutDBMode();
        }
    }

    /**
     * 读取数据库配置
     * @param $parameter
     * @return bool|array
     */
    private function getDatabase($parameter)
    {
        $databaseCheck = new Connections($parameter);
        return $databaseCheck->getConnection();
    }

    /**
     * 读取输入的命令
     * @return array
     */
    public function getInputArguments()
    {
        $parameter = $this->input->getOptions();
        if (!key_exists('table', $parameter) || !$parameter['table']) {
            $this->console->errorExit('--table【-t】为必填参数');
        }
        return $parameter;
    }

    /**
     * @param $parameter
     */
    private function askQuestion($parameter)
    {
        $fh = fopen('php://stdin', 'r');
        echo "未找到模型";
        $str = fread($fh, 1000);
    }
}
