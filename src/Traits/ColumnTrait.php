<?php
/**
 * @author wuqiangqiang
 * @date   2020/8/12
 */
namespace Uniondrug\Builder\Traits;

use Uniondrug\Postman\Parsers\Abstracts\Console;

trait ColumnTrait
{
    /**
     * Model类内容
     * @var string
     */
    private $content;
    /**
     * Table所有列
     * @var string
     */
    private $columns;
    /**
     * @var
     */
    public $console;

    /**
     * 合并到文件
     * @param array  $columns
     * @param array  $columnList
     * @param string $modleClass
     * @throws \ReflectionException
     */
    public function appendToFile(array $columns, array $columnList, string $modleClass)
    {
        $this->columns = $columns;
        $this->console = new Console();
        // 方法是否存在
        $ref = new \ReflectionClass($modleClass);
        if (!$columnList && $ref->hasMethod('columnMap')) {
            $this->console->error("{$this->table}表没有可生成的字段");
        }
        $constCombine = $columnMapVarCombine = $columnTextFuncCombine = [];
        if ($columnList) {
            $constCombine = implode("\n", array_column($columnList, 'const'));
            $columnMapVarCombine = implode("\n", array_column($columnList, 'columnMapVar'));
            $columnTextFuncCombine = implode("\n\n", array_column($columnList, 'columnTextFunc'));
        }
        // Model原先的内容
        $this->content = file_get_contents($ref->getFileName());
        $originLength = strlen($this->content);
        $constCombine && $this->combineConst($constCombine);
        $columnMapVarCombine && $this->combineColumnMapVar($columnMapVarCombine);
        $columnTextFuncCombine && $this->combineGetColumnTextFunc($columnTextFuncCombine);
        $isAppendColumnMap = false;
        // 表columnMap方法生成
        if (!$ref->hasMethod('columnMap')) {
            $this->combineColumnMapFunc($columnTextFuncCombine);
            $isAppendColumnMap = true;
        }
        // 更换的内容重写进去
        $changeLength = strlen($this->content);
        if ($originLength != $changeLength) {
            foreach ($columnList as $index => $column) {
                $tip = '';
                if (isset($column['const'])) {
                    $tip .= '[常量]';
                }
                if (isset($column['columnMapVar'])) {
                    $tip .= '[映射变量]';
                }
                if (isset($column['columnTextFunc'])) {
                    $tip .= '[文本方法]';
                }
                $tip && $this->console->info("{$index}字段{$tip}生成");
            }
            $isAppendColumnMap && $this->console->info("{$this->table}表columnMap方法生成");
            file_put_contents($ref->getFileName(), $this->content);
        }
    }

    /**
     * 替换const常量
     * const USER_NAME_TYPE_0 = 0; //停用
     * const USER_NAME_TYPE_1 = 1; //正常
     * @param string $const
     * @return string|string[]|null
     */
    public function combineConst(string $const)
    {
        if (preg_match_all("/(const).*(\/\/)*[\w]*/", $this->content, $match)) {
            $regx = end($match[0]);
            $this->content = preg_replace_callback("($regx)", function($a) use ($const){
                return $a[0].PHP_EOL.$const;
            }, $this->content);
        } else {
            $this->content = preg_replace_callback("/Model\s*\n*.*\{/", function($a) use ($const){
                return $a[0].PHP_EOL.$const;
            }, $this->content);
        }
    }

    /**
     * 合并状态列的关系映射方法
     * private static $_statusMap = [
     *      self::USER_STATUS_0 => '停用',
     *      self::USER_STATUS_1 => '正常'
     * ];
     * @param        $var
     * @return string|string[]|null
     */
    public function combineColumnMapVar($var)
    {
        if (preg_match_all('/(self::.*\=\>.*)/', $this->content, $match)) {
            $regx = end($match[0]);
            $this->content = preg_replace_callback("/($regx)[\,]?(\n\s+)+(\]\;)/", function($a) use ($var){
                return $a[0].PHP_EOL.$var;
            }, $this->content);
        } else if (preg_match_all("/(const).*(\/\/)*[\w]*/", $this->content, $match)) {
            $regx = end($match[0]);
            $this->content = preg_replace_callback("($regx)", function($a) use ($var){
                return $a[0].PHP_EOL.$var;
            }, $this->content);
        } else {
            $this->content = preg_replace_callback("/Model\s*\n*.*\{/", function($a) use ($var){
                return $a[0].PHP_EOL.$var;
            }, $this->content);
        }
    }

    /**
     * 合并获取列文本方法
     * public function getStatusText()
     * {
     *     return static::$_userStatusMap[$this->status] ?? static::$_unknowText;
     * }
     * @param string $function
     * @return string|string[]|null
     */
    public function combineGetColumnTextFunc(string $function)
    {
        // return static::$_userStatusMap[$this->user_status] ?? static::$_unknowText;
        if (preg_match_all('/(return)\s+(static::\$\w+\[.*\]).*/', $this->content, $match)) {
            $regx = end($match[0]);
            $regx = str_replace([
                '?',
                '_',
                '$',
                '[',
                ']'
            ], [
                '\?',
                '\_',
                '\$',
                '\[',
                '\]'
            ], $regx);
            $this->content = preg_replace_callback("/($regx)\s*\n*\}/", function($a) use ($function){
                return $a[0].PHP_EOL.PHP_EOL.$function;
            }, $this->content);
        } else if (preg_match_all('/(self::.*\=\>.*)/', $this->content, $match)) {
            $regx = end($match[0]);
            $this->content = preg_replace_callback("/($regx)[\,]?(\n\s+)+(\]\;)/", function($a) use ($function){
                return $a[0].PHP_EOL.PHP_EOL.$function;
            }, $this->content);
        } else {
            $this->content = preg_replace_callback("/\}[\s]*$/", function($a) use ($function){
                return $function.PHP_EOL.$a[0];
            }, $this->content);
        }
        //print_r($this->content);die;
    }

    /**
     * 合并列的别名方法
     * public function columnMap ()
     * {
     *      return [
     *          'userId' => 'userId',
     *          'user_name' => 'userName'
     *      ];
     * }
     * @return string|string[]|null
     */
    public function combineColumnMapFunc()
    {
        $columnMap = $this->getColumnMap();
        $columnMapFunc = PHP_EOL.<<<FUNC
    /**
     * return array
     */
    public function columnMap ()
    {
        return [
            $columnMap
        ];
    }
FUNC;
        $this->content = preg_replace_callback("/\}[\s]*$/", function($a) use ($columnMapFunc){
            return $columnMapFunc.PHP_EOL.$a[0];
        }, $this->content);
        return $this->content;
    }

    /**
     * 属性关系映射
     * @return string
     */
    public function getColumnMap()
    {
        $columanMap = "";
        foreach ($this->columns as $key => $value) {
            $columnName = $value['columnName'];
            $formateColumnName = preg_replace_callback("/[\_]+(\S)/", function($a){
                return strtoupper($a[1]);
            }, $value['columnName']);
            $columanMap .= "'$columnName' => '$formateColumnName',";
        }
        $columanMap = preg_replace_callback("/[\,]+/", function($a){
            return $a[0].PHP_EOL."\t\t\t";
        }, $columanMap);
        return trim($columanMap, " \t\n\r\0\x0B,");
    }

    /**
     * 获取带status或这type的列
     * @param array $columns
     * @return array
     */
    protected function getStatusOrTypeColumn(array $columns)
    {
        $columnArr = [];
        foreach ($columns as $key => $value) {
            if (preg_match('/(status)|(type)$/i', $value['columnName'], $match)) {
                $columnArr[] = $value;
            }
        }
        return $columnArr;
    }
}