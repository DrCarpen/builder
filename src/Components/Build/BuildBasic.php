<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-06
 */
namespace Uniondrug\Builder\Components\Build;

/**
 * Class BuildBasic
 * @package Uniondrug\Builder\Components\Build
 */
class BuildBasic extends Build
{
    /**
     * BuildBasic constructor.
     * @param $parameter
     */
    public function __construct($parameter)
    {
        parent::__construct($parameter);
    }

    /**
     * 构建各个子类的基础文件
     * @param $direct
     * @param $assign
     */
    public function initBuild($direct, $assign)
    {
        // 追加公共字段
        $assign = array_merge($assign, [
            'AUTHOR' => $this->getAuthorContent(),
            'CLASS_NAME' => $this->getClassName()
        ]);
        // 作者信息
        $authorContent = $this->getAuthorContent();
        // 方法类
        $className = $this->getClassName();
        // 获取模板
        $template = $this->getBasicTemplate();
        // 注入模板
        $fileContent = $this->templateParser->assign($assign, $template);
        // 生成文件
        $this->buildFile($fileContent, $this->getDocumentDirectPrefix(), $direct);
        $this->console->info('已生成'.$className.'基础文件');
    }
}