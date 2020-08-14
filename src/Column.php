<?php
/**
 * @author wuqiangqiang
 * @date   2020/8/11
 */
namespace Uniondrug\Builder;

use Uniondrug\Postman\Parsers\Abstracts\Console;

class Column
{
    /**
     * 列名称
     * @var string
     */
    public $columnName;
    /**
     * 列注释
     * @var string
     */
    public $columnComment;
    /**
     * 数据类型
     * @var string
     */
    public $dataType;
    /**
     * 列注释文本
     * @var string
     */
    public $columnCommentText;
    /**
     * 列注释类型数组
     * @var array
     */
    public $columnCommentType;
    /**
     * 列注释类型映射数组
     * @var array
     */
    public $columnCommentTypeMap;
    /**
     * @var array
     */
    public $columnList = [];
    /**
     * 模块类
     * @var string
     */
    public $modelClass;
    /**
     * 反射对象
     * @var string
     */
    public $ref;

    /**
     * Column constructor.
     * @param array  $column
     * @param string $class
     * @throws \ReflectionException
     */
    public function __construct(array $column, string $class)
    {
        $this->columnName = $column['columnName'];
        $this->columnComment = $column['columnComment'];
        $this->dataType = $column['dataType'];
        $this->modelClass = $class;
        // 方法是否存在
        $this->ref = new \ReflectionClass($class);
    }

    /**
     * @return bool|mixed
     * @throws \ReflectionException
     */
    public function handle()
    {
        if (!$this->columnComment) {
            return false;
        }
        $this->getColumnComment();
        if (!isset($this->columnCommentType)) {
            return false;
        }
        $this->makeConst();
        $this->makeColumnMapVar();
        $this->makeGetColumnTextFunc();
        return $this->columnList;
    }

    /**
     * 常量处理
     * @return bool|void
     */
    public function makeConst()
    {
        // 格式化后列名称
        $columnName = $this->getColumnName('const');
        // 列名称注释
        $constVarName = [];
        $constText = '';
        $count = 0;
        $flag = false;
        foreach ($this->columnCommentType as $typeValue => $typeText) {
            $constVar = $columnName.'_'.$typeValue;
            if (!$this->ref->hasConstant($constVar)) {
                if ($count == 0) {
                    $constText = "\t// {$this->columnCommentText}".PHP_EOL;
                }
                $constText .= "\tconst {$constVar} = {$typeValue}; //{$typeText}".PHP_EOL;
                $constVarName[] = $constVar;
                $flag = true;
            }
            $count++;
        }
        if ($flag) {
            $this->columnList['const'] = trim($constText, "\n\r\0\x0B");
            $this->columnList['constVarName'] = $constVarName;
        }
        return;
    }

    /**
     * 生成类型映射
     * @return bool|void
     */
    public function makeColumnMapVar()
    {
        if (!isset($this->columnCommentType)) {
            return false;
        }
        $constColumnName = $this->getColumnName('const');
        $funColumnName = $this->getColumnName('function');
        // 生成变量名称
        $columnMapVarName = '_'.$funColumnName.'Map';
        if ($this->ref->hasProperty($columnMapVarName)) {
            return false;
        }
        $columnCommentTypeMap = [];
        $columnCommentTypeMapStr = '';
        foreach ($this->columnCommentType as $typeValue => $typeText) {
            $columnCommentTypeMap[$constColumnName.'_'.$typeValue] = $typeText;
            $columnCommentTypeMapStr .= "self::".$constColumnName."_".$typeValue." => '".$typeText."',";
        }
        $columnCommentTypeMapStr = trim($columnCommentTypeMapStr, "\t\n\r\0\x0B,");
        $columnMapVar = <<<VAR
    // {$this->columnCommentText}映射
    private static \${$columnMapVarName} = [
        $columnCommentTypeMapStr
    ];
VAR;
        // 格式化变量结构
        $columnMapVar = preg_replace_callback("/[\,]+/", function($a){
            return $a[0].PHP_EOL."\t\t";
        }, $columnMapVar);
        $this->columnList['columnMapVar'] = $columnMapVar;
        $this->columnList['columnMapVarName'] = $columnMapVarName;
        return;
    }

    /**
     * 生成列类型的中文文本
     * @return bool|void
     */
    public function makeGetColumnTextFunc()
    {
        $funColumnName = $this->getColumnName('function');
        // 生成方法名称
        $columnTextFuncName = 'get'.ucfirst($funColumnName).'Text';
        if ($this->ref->hasMethod($columnTextFuncName)) {
            return false;
        }
        // 生成变量名称
        $columnMapVarName = '_'.$funColumnName.'Map';
        $columnTextFunc = <<<FUNC
    /**
     * {$this->columnCommentText}文本
     * return string
     */
    public function {$columnTextFuncName}()
    {
        return static::\${$columnMapVarName}[\$this->$this->columnName] ?? static::\$_unknowText;
    }
FUNC;
        $this->columnList['columnTextFunc'] = $columnTextFunc;
        $this->columnList['columnTextFuncName'] = $columnTextFuncName;
        return;
    }

    /**
     * @return bool|void
     */
    public function getColumnComment()
    {
        // 列注释文本
        $this->columnComment = trim($this->columnComment, "\ \t\n\r\0\x0B").'|';
        if (preg_match('/[\x{4e00}-\x{9fa5}]+\:(\d+\=[\x{4e00}-\x{9fa5}]+\|)+/ui', $this->columnComment, $match)) {
            if (strlen($this->columnComment) != strlen($match[0])) {
                return (new Console())->warning("注释不符合规范");
            }
        }
        $columnCommentArr = preg_split('/[\:\|]/', $this->columnComment);
        $columnCommentArr = array_filter($columnCommentArr, function($v){
            return $v !== '' && $v !== null;
        });
        $columnCommentArr = array_values($columnCommentArr);
        // 只有列注释文本
        if (count($columnCommentArr) == 1) {
            return false;
        }
        // 列注释文本
        $columnCommentText = current($columnCommentArr);
        unset($columnCommentArr[0]);
        // 列注释类型数组
        $columnCommentType = [];
        // 列注释类型映射数组
        $columnCommentTypeMap = [];
        foreach ($columnCommentArr as $key => $value) {
            if (is_numeric($value)) {
                $columnCommentType[$value] = $columnCommentArr[$key + 1];
            } else {
                $newValue = preg_split('/[\=]/', $value);
                $columnCommentType[$newValue[0]] = $newValue[1];
            }
        }
        $this->columnCommentType = $columnCommentType;
        $this->columnCommentText = $columnCommentText;
        return;
    }

    /**
     * 格式化列名称
     * @param string $type
     * @return string|string[]|null
     */
    public function getColumnName(string $type)
    {
        // 常量列名
        if (in_array($type, [
            'const'
        ])) {
            $columnName = strtoupper($this->columnName);
        } else {
            $columnName = preg_replace_callback("/[\_]+(\S)/", function($a){
                return strtoupper($a[1]);
            }, $this->columnName);
        }
        return $columnName;
    }
}