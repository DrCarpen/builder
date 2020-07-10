<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-06
 */
namespace Uniondrug\Builder\Components\Build;

use Uniondrug\Builder\Components\Build\BuildBasic;

class BuildRequest extends BuildBasic
{
    public function __construct($parameter)
    {
        parent::__construct($parameter);
        $this->classType = 'Request';
    }

    public function build($columns)
    {
        // 获取文件名称
        $direct = $this->getDocumentDirectPrefix().$this->getFileName();
        // 判断基础文件是否存在
        if (!$this->checkFileExsit($direct)) {
            $this->initBuild($direct, [
                'TABLE_NAME' => $this->_tableName(),
                'EXTEND_CLASS' => $this->api == 'page' ? 'PagingRequest' : 'Struct',
                'REQUEST_BODY' => $this->getRequestBody($columns)
            ]);
        }
        return true;
    }

    /**
     * @param $columns
     * @return string
     */
    protected function getRequestBody($columns)
    {
        $template = $this->getPartTemplate();
        $templateList = [];
        foreach ($columns as $key => $value) {
            if ($value['columnKey'] != 'PRI' && !in_array($value['COLUMN_NAME'], [
                    'gmtCreated',
                    'gmtUpdated'
                ])) {
                $repalceList = [
                    'COLUMN_COMMENT' => $value['columnComment'] ? $value['columnComment'] : $value['columnName'],
                    'VALIDATOR_TYPE' => $this->getValidator($this->getType($value['dataType']), $value),
                    'DATA_TYPE' => $this->getType($value['dataType']),
                    'COLUMN_NAME' => $value['columnName']
                ];
                $templateList[] = $this->templateParser->assign($repalceList, $template);
            }
        }
        return implode(PHP_EOL, $templateList);
    }
}
