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
    /**
     * BuildController constructor.
     * @param $parameter
     */
    public function __construct($parameter)
    {
        parent::__construct($parameter);
        $this->classType = 'Controller';
    }

    /**
     * @param $columns
     * @return bool
     * @throws \ReflectionException
     */
    public function build($columns)
    {
        // 获取文件名称
        $direct = $this->getDocumentDirectPrefix().$this->getFileName();
        // 判断初试文件是否存在
        if (!$this->checkFileExsit($direct)) {
            $this->initBuild($direct, ['ROUTE_PRIFIX' => $this->getRoutelPrefix($this->table)]);
        }
        $this->appendAPI($direct);
        return true;
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
     * 获取sdk注释
     * @return string
     */
    protected function getSdkName()
    {
        return lcfirst($this->_tableName()).ucfirst($this->api);
    }

    /**
     * 检查方法名是否存在
     * @return bool
     * @throws \ReflectionException
     */
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

    /**
     * 追加接口的action
     * @param $direct
     */
    public function appendAPI($direct)
    {
        // 检查接口是否存在
        if ($this->checkActionExist()) {
            $this->console->errorExit($this->getClassName().'控制器中已经存在此接口');
        }
        // 读取文件
        $initFile = $this->getInitFile($direct);
        if (!$initFile) {
            $this->console->warning('Controller基础文件不存在!');
            return false;
        }
        // 创建接口数据
        $controllerBody = $this->getPartTemplate();
        $controllerBodyFile = $this->templateParser->assign([
            'API_NAME' => $this->getApiName(),
            'SDK_NAME' => $this->getSdkName(),
            'MIN_API' => $this->api,
            'MAX_API' => ucfirst($this->api),
            'TABLE_NAME' => $this->_tableName(),
        ], $controllerBody);
        // 追加接口
        $newFile = substr_replace($initFile, PHP_EOL.PHP_EOL.$controllerBodyFile.'}', strrpos($initFile, '}') - 1, strrpos($initFile, '}'));
        // 追加命名空间
        $baseText = 'use App\Controllers\Abstracts\Base;';
        $text = $baseText.PHP_EOL.'use App\Logics\\'.$this->_tableName().'\\'.ucfirst($this->api).'Logic;';
        $newFile = str_replace($baseText, $text, $newFile);
        $this->rewriteFile($direct, $newFile);
        $this->console->info('Controller中已追加API!');
    }

    /**
     * @param $table
     * @return mixed
     */
    private function getRoutelPrefix($table)
    {
        return str_replace('_', '/', $table);
    }
}