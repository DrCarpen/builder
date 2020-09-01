<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-06
 */
namespace Uniondrug\Builder\Components\Build;

use Phalcon\Db\Exception;
use Uniondrug\Builder\Column;
use Uniondrug\Builder\Components\Build\BuildBasic;
use Uniondrug\Builder\Traits\ColumnTrait;

/**
 * Class BuildModel
 * @package Uniondrug\Builder\Components\Build
 */
class BuildModel extends BuildBasic
{
    use ColumnTrait;
    /**
     * @var array
     */
    public $parameter;

    /**
     * BuildModel constructor.
     * @param $parameter
     */
    public function __construct($parameter)
    {
        parent::__construct($parameter);
        $this->classType = 'Model';
        $this->parameter = $parameter;
    }

    /**
     * @param array $columns
     * @throws Exception
     * @throws \ReflectionException
     */
    public function build(array $columns)
    {
        // 获取文件名称
        $direct = $this->getDocumentDirectPrefix().$this->getFileName();
        $oldDirect = $this->getDocumentDirectPrefix().$this->getOldFileName();
        // 判断目录是否存在
        if (!$this->checkFileExsit($direct) && !$this->checkFileExsit($oldDirect)) {
            $init = [
                'PROPERTY_TEMPLATE_LIST' => $this->getPropertyContent($columns),
                'COLUMN_MAP' => $this->getColumnMap($columns)
            ];
            // 注解列表
            $this->initBuild($direct, $init);
        }
    }

    /**
     * @return string
     */
    private function getColumnMap($columns)
    {
        $columnMap = [];
        foreach ($columns as $column) {
            $columnMap[] = '            \''.$column['columnName'].'\' => \''.$column['camelColumnName'].'\'';
        }
        $columnMapContent = implode(','.PHP_EOL, $columnMap);
        return $this->templateParser->assign(['COLUMN_MAP' => $columnMapContent], $this->getPartTemplate('ModeColumnMap'));
    }
}