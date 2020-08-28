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

class BuildModel extends BuildBasic
{
    use ColumnTrait;
    /**
     * @var array
     */
    public $parameter;

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
                'PROPERTY_TEMPLATE_LIST' => $this->getPropertyContent($columns)
            ];
            // 注解列表
            $this->initBuild($direct, $init);
        }
        // 是否输入表列名
//        if (isset($this->parameter['column'])) {
//            $inputColumn = $this->parameter['column'];
//            $columnNameArr = array_column($columns, 'columnName');
//            $newColumnList = array_column($columns, null, 'columnName');
//            if (!in_array($inputColumn, $columnNameArr)) {
//                throw new Exception("The table $this->table does not have this field");
//            }
//            if (!preg_match('/(status)|(type)$/i', $inputColumn)) {
//                throw new Exception("This field should end with status or type, for exapmle user_status");
//            }
//            if (empty($newColumnList[$inputColumn]['columnComment'])) {
//                throw new Exception("This field does not have comment, for example 用户类型:0=停用|1=正常");
//            }
//            $columnArr[] = $newColumnList[$inputColumn];
//        } else {
//            // 所有状态类型列
//            $columnArr = $this->getStatusOrTypeColumn($columns);
//        }
//        $className = '\\App\\Models\\'.$this->getClassName($this->table);
//        // 包含状态类型的列数组
//        $columnList = [];
//        array_walk($columnArr, function($column) use (&$columnList, $className){
//            if ($column['columnComment']) {
//                $result = (new Column($column, $className))->handle();
//                if ($result) {
//                    $columnList[$column['columnName']] = $result;
//                }
//            }
//        });
//        // 追加到文件
//        $this->appendToFile($columns, $columnList, $className);
    }
}