<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-06
 */
namespace Uniondrug\Builder\Components\Build;

use Uniondrug\Builder\Components\Build\BuildBasic;

/**
 * Class BuildRequest
 * @package Uniondrug\Builder\Components\Build
 */
class BuildRequest extends BuildBasic
{
    /**
     * BuildRequest constructor.
     * @param $parameter
     */
    public function __construct($parameter)
    {
        parent::__construct($parameter);
        $this->classType = 'Request';
    }

    /**
     * @param $columns
     * @return bool
     */
    public function build($columns)
    {
        // 获取文件名称
        $direct = $this->getDocumentDirectPrefix().$this->getFileName();
        // 判断基础文件是否存在
        if (!$this->checkFileExsit($direct)) {
            // 创建基础文件
            $this->initBuild($direct, [
                'TABLE_NAME' => $this->_tableName(),
                'EXTEND_CLASS' => $this->api == 'page' ? 'PagingRequest' : 'Struct',
                'REQUEST_BODY' => $this->getRequestBody($columns)
            ]);
        }
        return true;
    }

    /**
     * 读取入参结构体的字段内容
     * @param $columns
     * @return string
     */
    protected function getRequestBody($columns)
    {
        $template = $this->getPartTemplate();
        $templateList = [];
        if (!$columns) {
            return '';
        }
        foreach ($columns as $key => $value) {
            // 过滤不需要的字段
            if ($value['columnKey'] == 'PRI' || in_array($value['camelColumnName'], [
                    'gmtCreated',
                    'gmtUpdated'
                ])) {
                continue;
            }
            $repalceList = [
                'COLUMN_COMMENT' => $value['columnComment'] ? $value['columnComment'] : $value['camelColumnName'],
                'VALIDATOR_TYPE' => $this->getValidator($this->getType($value['dataType']), $value),
                'DATA_TYPE' => $this->getType($value['dataType']),
                'COLUMN_NAME' => $value['camelColumnName']
            ];
            $templateList[] = $this->templateParser->assign($repalceList, $template);
        }
        return implode(PHP_EOL, $templateList);
    }
}
