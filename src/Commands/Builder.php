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
                            {--mode=api : 发布markdown文档}
                            {--path=docs/api : markdown文档存储位置}';

    /**
     * @inheritdoc
     */
    public function handle()
    {
        $path = getcwd();
        echo $path;
        die;
        $collection = new Collection($path);
        $collection->parser();
    }
}
