<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-06
 */
namespace Uniondrug\Builder\Components\Build;

use Uniondrug\Builder\Components\Build\BuildBasic;

class BuildLogic extends BuildBasic
{
    public function __construct($parameter)
    {
        parent::__construct($parameter);
        $this->classType = 'Logic';
    }

    public function build($columns)
    {
        // 获取文件名称
        $direct = $this->getDocumentDirectPrefix($this->classType).$this->getFileName($this->classType);
        // 判断初试文件是否存在
        if (!$this->checkFileExsit($direct)) {
            $this->initBuild($direct);
        }
        return true;
    }

    public function initBuild($direct)
    {
        // 作者信息
        $authorContent = $this->getAuthorContent();
        // 方法类
        $className = $this->getClassName($this->classType);
        // 获取模板
        $template = $this->getBasicTemplate($this->classType);
        // 注入模板
        $fileContent = $this->templateParser->assign([
            'AUTHOR' => $authorContent,
            'CLASS_NAME' => $className,
            'TABLE_NAME' => $this->_tableName(),
            'MIN_TABLE_NAME' => lcfirst($this->_tableName()),
            'MAX_API' => ucfirst($this->api),
            'MIN_API' => $this->api,
        ], $template);
        // 生成文件
        $this->buildFile($fileContent, $this->getDocumentDirectPrefix($this->classType), $direct);
    }
}