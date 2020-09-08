<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-06
 */
namespace Uniondrug\Builder\Components\Build;

/**
 * Class BuildTrait
 * @package Uniondrug\Builder\Components\Build
 */
class BuildTrait extends Base
{
    /**
     * BuildTrait constructor
     * @param $parameter
     */
    public function __construct($parameter)
    {
        parent::__construct($parameter);
        $this->classType = 'Trait';
    }

    /**
     * 构造trait文件的主函数
     * @param $columns
     * @return bool
     */
    public function build($columns)
    {
        // 获取文件名称
        $direct = $this->getDocumentDirectPrefix().$this->getFileName();
        // 判断基础文件是否存在
        if (!$this->checkFileExsit($direct)) {
            $this->initBuild($direct, [
                'TRAIT_LIST' => $this->getTraitList($columns),
                'TRAIT_NAME' => $this->_tableName().'Trait'
            ]);
        }
        return true;
    }

    /**
     * 获取trait的字段内容
     * @param $columns
     * @return array
     */
    protected function getTraitList($columns)
    {
        $propertyTemplate = $this->getPartTemplate();
        $propertyTemplateList = [];
        foreach ($columns as $key => $value) {
            // 过滤不需要的字段
            if (in_array($value['camelColumnName'], [
                'gmtCreated',
                'gmtUpdated'
            ])) {
                continue;
            }
            $replaceList = [
                'COLUMN_COMMENT' => $value['columnComment'] ? $value['columnComment'] : $value['camelColumnName'],
                'DATA_TYPE' => $this->getType($value['dataType']),
                'COLUMN_NAME' => $value['camelColumnName']
            ];
            $propertyTemplateList[] = $this->templateParser->assign($replaceList, $propertyTemplate);
        }
        return implode(PHP_EOL, $propertyTemplateList);
    }
}