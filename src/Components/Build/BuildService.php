<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-06
 */
namespace Uniondrug\Builder\Components\Build;

use Uniondrug\Builder\Components\Build\BuildBasic;
use App\Services\Abstracts\ServiceTrait;

/**
 * Class BuildService
 * @package Uniondrug\Builder\Components\Build
 */
class BuildService extends BuildBasic
{
    /**
     * BuildService constructor.
     * @param $parameter
     */
    public function __construct($parameter)
    {
        parent::__construct($parameter);
        $this->classType = 'Service';
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
            $this->initBuild($direct, ['TABLE_NAME' => lcfirst($this->_tableName())]);
            // 更新serviceTrait文件
//            $this->rewriteServiceTrait();
        }
        // 追加API
        $this->appendAPI($direct);
        return true;
    }

    /**
     * 重写serviceTrait文件
     * @throws \ReflectionException
     */
    public function rewriteServiceTrait()
    {
        $name = $this->getClassName();
        try {
            $service = new \ReflectionClass(ServiceTrait::class);
        } catch(\Exception $exception) {
            return false;
        }
        //更改注解
        $preDocument = $service->getDocComment();
        $propertyText = "* @property ".$name." $".lcfirst($name);
        $propertyText .= PHP_EOL." */";
        $newDocument = str_replace('*/', $propertyText, $preDocument);
        //更改use
        $oldUseText = "namespace App\Services\Abstracts;".PHP_EOL;
        $newUseText = $oldUseText.PHP_EOL.'use App\\Services\\'.$name.';';
        $filename = $service->getFileName();
        $oldFlie = file_get_contents($filename);
        $newFlie = str_replace($preDocument, $newDocument, $oldFlie);
        $newFlie = str_replace($oldUseText, $newUseText, $newFlie);
        $this->console->info($this->className." 写入 ServiceTrait");
        file_put_contents($filename, $newFlie);
        $this->console->info('已更新ServiceTrait文件');
    }

    /**
     * 追加service中的方法
     * @param $direct
     */
    public function appendAPI($direct)
    {
        // 读取文件
        $initFile = $this->getInitFile($direct);
        if (!$initFile) {
            $this->console->info('Service基础文件不存在！');
        }
        // 创建接口数据
        $partBody = $this->getPartTemplate();
        $partBodyFile = $this->templateParser->assign([
            'MIN_API' => $this->api,
            'MAX_API' => ucfirst($this->api)
        ], $partBody);
        // 追加接口
        $newFile = substr_replace($initFile, PHP_EOL.PHP_EOL.$partBodyFile.'}', strrpos($initFile, '}') - 1, strrpos($initFile, '}'));
        // 追加命名空间
        $baseText = 'use App\Services\Abstracts\Service;';
        $text = $baseText.PHP_EOL.'use App\Structs\Requests\\'.$this->_tableName().'\\'.ucfirst($this->api).'Request;';
        $newFile = str_replace($baseText, $text, $newFile);
        $this->rewriteFile($direct, $newFile);
        $this->console->info('已更新Service文件');
    }
}
