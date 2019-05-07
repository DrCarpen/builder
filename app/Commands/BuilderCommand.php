<?php
/**
 * @author wsfuyibing <websearch@163.com>
 * @date   2018-10-30
 */
namespace App\Commands;

use Uniondrug\Builder\Commands\Builder;

/**
 * 生成项目级文档
 * <code>
 * php console postman
 * </code>
 * @package App\Commands
 */
class BuilderCommand extends Builder
{
    /**
     * 命令名称
     * @var string
     */
    protected $signature = 'builder';
    /**
     * 命令描述
     * @var string
     */
    protected $description = '导出Postman/Markdown文档、SDK模板';

    /**
     * @inheritdoc
     */
    public function handle()
    {
//        var_dump(
//            $this->config->path('database'),get_class_methods(app()),app()->appPath().'/'
//        );exit();
        parent::handle();
    }
}
