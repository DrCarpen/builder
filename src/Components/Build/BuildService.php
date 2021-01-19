<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-06
 */
namespace Uniondrug\Builder\Components\Build;

use App\Services\Abstracts\ServiceTrait;


/**
 * Class BuildService
 * @package Uniondrug\Builder\Components\Build
 */
class BuildService extends Base
{
    private $columns = '';
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
     * @return bool
     */
    public function build($columns)
    {
        $camelColumnName = $columns ? array_column($columns, 'camelColumnName') : [];
        $this->columns = $camelColumnName;
        // 获取文件名称
        $direct = $this->getDocumentDirectPrefix().$this->getFileName();
        // 判断初试文件是否存在
        if (!$this->checkFileExsit($direct)) {
            $this->initBuild($direct, ['TABLE_NAME' => lcfirst($this->_tableName())]);
        }
        //判断方法是否已存在
        if ($this->checkMethodExist()){
            return;
        }
        // 追加API
        if ($this->canAppend($this->api) && $columns){
            $this->appendAPI($direct);
        }
        //注册service
        $this->rewriteServiceTrait();
        return true;
    }

    /**
     * 重写serviceTrait文件
     * @return bool
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
        $filename = $service->getFileName();
        $oldFile = file_get_contents($filename);
        $preDocument = $service->getDocComment();
        $propertyText = "* @property ".$name." $".lcfirst($name);
        if (strstr($preDocument, $propertyText) || strstr($oldFile, $propertyText)){
            $this->console->info('ServiceTrait 已存在注解:'.$name);
            return;
        }
        //更改use
        $oldUseText = "namespace App\Services\Abstracts;".PHP_EOL;
        $newUseText = $oldUseText.PHP_EOL.'use App\\Services\\'.$name.';';

        $division = '* @package App\Services\Abstracts'.PHP_EOL;
        $oldFileArr = explode($division, $oldFile);
        $newFile = $oldFileArr[0].$division.' '.$propertyText.PHP_EOL.$oldFileArr[1];
        $this->console->info(" 写入 ServiceTrait");
        $delimiter = 'use App\Servers\Http;'.PHP_EOL;
        $newFileArr = explode($delimiter, $newFile);
        $useText    = "use App\Services\\".$name.';'.PHP_EOL;
        $newFile = $newFileArr[0].$delimiter.$useText.$newFileArr[1];
        $newleArr = explode($division, $newFile);
        file_put_contents($filename, $newFile);
        $this->console->info('已更新ServiceTrait文件');
        return true;
    }

    /**
     * 追加service中的方法
     * @param $direct
     */
    private function appendAPI($direct)
    {
        // 读取文件
        $initFile = $this->getInitFile($direct);
        // 创建接口数据
        $partBodyFile = $this->templateParser->assign($this->getAssignArr($this->api), $this->getPartTemplate($this->api));
        // 追加接口
        $newFile = substr_replace($initFile, PHP_EOL.PHP_EOL.$partBodyFile.'}', strrpos($initFile, '}') - 1, strrpos($initFile, '}'));
        // 追加命名空间
        $baseText = 'use App\Services\Abstracts\Service;';
        $useTableStr = 'use App\Models\\'.$this->_tableName().';';
        $fileArr =  explode($baseText, $newFile);
        $userStr = [$baseText];
        if (!strstr($newFile, $useTableStr)){
            $userStr[] = $useTableStr;
        }
        if (in_array($this->api, ['detail','delete', 'create', 'update'])){
            if (!strstr($initFile, 'use App\Errors\Code')){
                $userStr[] = 'use App\Errors\Code;';
            }
            if (!strstr($initFile, 'use App\Errors\Error')){
                $userStr[] = 'use App\Errors\Error;';
            }
        }
        $requsetStr = 'use App\Structs\Requests\\'.$this->_tableName().'\\'.ucfirst($this->api).'Request;';
        if (!strstr($newFile, $requsetStr) && !in_array($this->api, ['detail','delete']) && $this->canAppend($this->api)){
            $userStr[] = $requsetStr;
        }
        $newFile = trim($fileArr[0]).PHP_EOL.implode(PHP_EOL, $userStr).PHP_EOL.trim($fileArr[1]);
        $this->rewriteFile($direct, $newFile);
        $this->console->info('已在'.$this->_tableName().'Service文件添加方法:'.$this->api);
    }

    /**
     * service 方法内容
     * @param $api
     * @return array
     */
    private function getAssignArr($api)
    {
        switch ($api){
            case 'create':
            case 'c':
            case 'update':
            case 'u':
                $assign = [
                    'MIN_API'      => $this->api,
                    'MAX_API'      => ucfirst($this->api),
                    'TABLE_NAME'   => $this->_tableName(),
                    'COLUMN_BODY'  => $this->getColumnBody()
                ];
                break;
            case 'delete':
            case 'd':
            case 'detail':
            case 'r':
                $assign = [
                    'TABLE_NAME'   => $this->_tableName(),
                ];
                break;
            case 'l':
            case 'listing':
            case 'p':
            case 'paging':
                $assign = [
                    'MIN_API'          => $this->api,
                    'MAX_API'          => ucfirst($this->api),
                    'TABLE_NAME'       => $this->_tableName(),
                    'CONDITIONS_BODY'  => $this->getPageBody()
                ];
                break;
            default:
                $assign = [
                    'MIN_API'      => $this->api,
                    'MAX_API'      => ucfirst($this->api)
                ];
        }
        return $assign;
    }

    /**
     * 拼凑对象属性
     * @return mixed|string
     */
    private function getColumnBody()
    {
        $body = '';
        foreach ($this->columns as $num => $column){
            if (!in_array($column, ['id', 'gmtCreated', 'gmtUpdated'])){
                $space = '        ';
                $body .= $space.'$model->'.$column.' = $request->'.$column.';'.PHP_EOL;
            }
        }
        return $body;
    }

    /**
     * 拼凑Page 函数体
     * @return mixed|string
     */
    private function getPageBody()
    {
        $body = '';
        foreach ($this->columns as $num => $column){
            if (!in_array($column, ['id', 'gmtCreated', 'gmtUpdated'])) {
                $condition = '        if ($request->' . $column . ') {' . PHP_EOL;
                $condition .= '            $conditions[] = " ' . $column . ' = \'{$request->' . $column . '}\'";' . PHP_EOL;
                $condition .= '        }' . PHP_EOL;
                $body .= $condition;
            }
        }
        return $body;
    }

    /**
     * service create 方法内容
     * @return string
     */
    private function getCreateAssign()
    {
        $table  = $this->rename ?? $this->table;
        $create = '$'.$this->table.' = new '.ucfirst($this->table).'();'.PHP_EOL;
        $space  = '        ';
        foreach ($this->columns as $column){
            if (!in_array($column, ['id', 'gmtCreated', 'gmtUpdated'])){
                $create .= $space.'$'.$this->table.'->'.$column.' = $request->'.$column.';'.PHP_EOL;
            }
        }
        $create .= $space.'if ($'.$this->table.'->save()){'.PHP_EOL;
        $create .= $space.'    return $'.$this->table.';'.PHP_EOL;
        $create .= $space.'}'.PHP_EOL;
        return $create .= $space.'throw new Error(Code::FAILURE_CREATE);';
    }

    /**
     * 检查方法名是否存在
     * @return bool
     * @throws \ReflectionException
     */
    protected function checkMethodExist()
    {
        // 判断方法是否存在
        $class = '\App\Services\\'.$this->_tableName().'Service';
        $service = new \ReflectionClass($class);
        $methods = $service->getMethods();
        foreach ($methods as $method) {
            if ($method->name == $this->api) {
                $this->console->info($this->_tableName().'Service'.'已存在方法'.$this->api);
                return true;
            }
        }
        return false;
    }

    /**
     * 是否可追加
     * @param $api
     * @return bool
     */
    private function canAppend($api)
    {
        return in_array($api, ['create', 'update', 'detail', 'paging', 'listing', 'delete']);
    }
}
