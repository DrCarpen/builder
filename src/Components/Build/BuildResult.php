<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-06
 */
namespace Uniondrug\Builder\Components\Build;

use Uniondrug\Builder\Components\Build\BuildBasic;

class BuildResult extends BuildBasic
{
    public function __construct($parameter)
    {
        parent::__construct($parameter);
        $this->classType = 'Result';
    }

    public function build($columns)
    {
        // 获取文件名称
        $direct = $this->getDocumentDirectPrefix().$this->getFileName();
        // 判断基础文件是否存在
        if ($this->checkFileExsit($direct)) {
            return false;
        }
        $this->initBuild($direct, [
            'TABLE_NAME' => $this->_tableName(),
            'EXTEND_CLASS' => $this->getExtendClass(),
            'USE_TRAIT' => $this->getUseTrait(),
            'RESULT_PART' => $this->getResultPart()
        ]);
        // 创建Row
        if (in_array($this->api, [
            'listing',
            'page'
        ])) {
            $rowDirect = $this->getDocumentDirectPrefix().$this->getFileName(1);
            $this->api = 'row';
            $this->initBuild($rowDirect, [
                'TABLE_NAME' => $this->_tableName(),
                'EXTEND_CLASS' => $this->getExtendClass(),
                'USE_TRAIT' => $this->getUseTrait(),
                'RESULT_PART' => $this->getResultPart()
            ]);
        }
        return true;
    }

    protected function getExtendClass()
    {
        if ($this->api == 'page') {
            return 'PaginatorStruct';
        } else if ($this->api == 'listing') {
            return 'ListStruct';
        } else {
            return 'Struct';
        }
    }

    protected function getUseTrait()
    {
        if (in_array($this->api, [
            'page',
            'listing'
        ])) {
            return '';
        } else {
            return 'use App\Structs\Traits\\'.$this->_tableName().'Trait;';
        }
    }

    protected function getResultPart()
    {
        if (in_array($this->api, [
            'page',
            'listing'
        ])) {
            return $this->getPartTemplate();
        } else {
            return '     use '.$this->_tableName().'Trait;';
        }
    }
}