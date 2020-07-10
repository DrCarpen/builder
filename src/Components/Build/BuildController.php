<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-06
 */
namespace Uniondrug\Builder\Components\Build;

use Uniondrug\Builder\Components\Build\BuildBasic;

/**
 * Class BuildController
 * @package Uniondrug\Builder\Components\Build
 */
class BuildController extends BuildBasic
{
    public function __construct($parameter)
    {
        parent::__construct($parameter);
        $this->classType = 'Controller';
    }

    public function build($columns)
    {
        // 获取文件名称
        $direct = $this->getDocumentDirectPrefix($this->classType).$this->getFileName($this->classType);
        // 判断初试文件是否存在
        if (!$this->checkFileExsit($direct)) {
            $this->initBuild($direct);
        }
        if ($this->checkActionExist()) {
            $this->console->error('此接口已存在');
            return false;
        }
        // 读取文件
        $initFile = $this->getInitFile($direct);
        // 创建接口数据
        $controllerBody = $this->getPartTemplate($this->classType);
        $controllerBodyFile = $this->templateParser->assign([
            'API_NAME' => $this->getApiName(),
            'SDK_NAME' => $this->getSdkName(),
            'MIN_API' => $this->api,
            'MAX_API' => ucfirst($this->api),
            'TABLE_NAME' => $this->_tableName(),
        ], $controllerBody);
        // 追加接口
        $newFile = preg_replace('/\}$/', $controllerBodyFile.'}', $initFile);
        // 追加命名空间
        $baseText = 'use App\Controllers\Abstracts\Base;';
        $text = $baseText.PHP_EOL.'use App\Logics\\'.$this->_tableName().'\\'.ucfirst($this->api).'Logic;';
        $newFile = str_replace($baseText, $text, $newFile);
        $this->rewriteFile($direct, $newFile);
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
            'TABLE_NAME' => lcfirst($this->_tableName()),
        ], $template);
        // 生成文件
        $this->buildFile($fileContent, $this->getDocumentDirectPrefix($this->classType), $direct);
    }

    /**
     * 获取当前API名称
     * @return mixed|string
     */
    protected function getApiName()
    {
        if (key_exists($this->api, $this->apiNameMapping)) {
            return $this->apiNameMapping[$this->api];
        }
        return $this->api;
    }

    /**
     * @return string
     */
    protected function getSdkName()
    {
        return lcfirst($this->_tableName()).ucfirst($this->api);
    }

    protected function checkActionExist()
    {
        // 判断方法是否存在
        $class = '\App\Controllers\\'.$this->_tableName().'Controller';
        $service = new \ReflectionClass($class);
        $methods = $service->getMethods();
        foreach ($methods as $method) {
            if ($method->name == $this->api.'Action') {
                return true;
            }
        }
        return false;
    }
}