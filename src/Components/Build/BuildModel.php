<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-06
 */
namespace Uniondrug\Builder\Components\Build;

use Uniondrug\Builder\Components\Build\BuildBasic;

class BuildModel extends BuildBasic
{
    public $classType = 'Model';

    public function __construct($parameter)
    {
        parent::__construct($parameter);
    }

    /**
     * @param $columns
     * @return bool
     */
    public function build($columns)
    {
        // 获取文件名称
        $direct = $this->getDocumentDirectPrefix($this->classType).$this->getFileName($this->classType);
        // 判断目录是否存在
        if ($this->checkFileExsit($direct)) {
            $this->console->warning('Model文件已存在！');
            return false;
        }
        // 作者信息
        $authorContent = $this->getAuthorContent();
        // 方法类
        $className = $this->getClassName($this->classType);
        // 注解列表
        $propertyContent = $this->getPropertyContent($columns);
        // 获取模板
        $template = $this->getTemplate($this->classType);
        // 注入模板
        $fileContent = $this->templateParser->assign([
            'AUTHOR' => $authorContent,
            'PROPERTY_TEMPLATE_LIST' => $propertyContent,
            'CLASS_NAME' => $className
        ], $template);
        // 生成文件
        $this->buildFile($fileContent, $this->getDocumentDirectPrefix($this->classType), $direct);
        return true;
    }
}