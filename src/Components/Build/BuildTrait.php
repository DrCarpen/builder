<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-06
 */
namespace Uniondrug\Builder\Components\Build;

use Uniondrug\Builder\Components\Build\BuildBasic;

class BuildTrait extends BuildBasic
{
    public function __construct($parameter)
    {
        parent::__construct($parameter);
        $this->classType = 'Trait';
    }

    public function build($columns)
    {
        // 获取文件名称
        $direct = $this->getDocumentDirectPrefix().$this->getFileName();
        // 判断基础文件是否存在
        if (!$this->checkFileExsit($direct)) {
            $this->initBuild($direct, [
                'TRAIT_LIST' => $this->getTraitList($columns)
            ]);
        }
        return true;
    }

    /**
     * @param $columns
     * @return array
     */
    protected function getTraitList($columns)
    {
        $propertyTemplate = $this->getPartTemplate();
        $propertyTemplateList = [];
        foreach ($columns as $key => $value) {
            $replaceList = [
                'COLUMN_COMMENT' => $value['columnComment'] ? $value['columnComment'] : $value['columnName'],
                'DATA_TYPE' => $this->getType($value['dataType']),
                'COLUMN_NAME' => $value['columnName']
            ];
            $propertyTemplateList[] = $this->templateParser->assign($replaceList, $propertyTemplate);
        }
        return implode(PHP_EOL, $propertyTemplateList);
    }
}