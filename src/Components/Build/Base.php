<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-06
 */
namespace Uniondrug\Builder\Components\Build;

use Uniondrug\Builder\Components\Tools\Console;
use Uniondrug\Builder\Components\Tools\TemplateParser;

/**
 * Class Base
 * @package Uniondrug\Builder\Components\Build
 */
class Base
{
    /**
     * 接口名称
     * @var string
     */
    public $api;
    /**
     * 接口的表名
     * @var string
     */
    public $table;
    /**
     * 别名
     * @var string
     */
    public $rename;
    /**
     * 表前缀
     * @var string
     */
    public $pre;
    /**
     * @var Console
     */
    public $console;
    /**
     * 控制器路径
     * @var path
     */
    public $path;
    /**
     * 模板引擎
     * @var TemplateParser
     */
    public $templateParser;
    /**
     * 文件类型
     * 在各个子类中定义
     * @var string
     */
    public $classType;
    /**
     * 结构体中int映射的数据库类型表
     * @var array
     */
    protected $int = [
        'int',
        'integer',
        'tinyint',
        'smallint',
        'mediumint',
        'bigint'
    ];
    /**
     * 结构体中string映射的数据库类型表
     * @var array
     */
    protected $string = [
        'char',
        'varchar',
        'text',
        'tinytext',
        'mediumtext',
        'longtext',
        'json'
    ];
    /**
     * 结构体中float映射的数据库类型表
     * @var array
     */
    protected $float = [
        'double',
        'float',
        'decimal'
    ];
    // 日期类型包含的子类型
    /**
     * 结构体中日期映射的数据库类型表
     * @var array
     */
    protected $time = [
        'date',
        'datetime',
        'year',
        'time'
    ];
    /**
     * 结构体中时间戳映射的数据库类型表
     * @var array
     */
    protected $timestamp = [
        'timestamp'
    ];
    /**
     * 接口名称映射表
     * @var array
     */
    protected $apiNameMapping = [
        'create' => '新增',
        'delete' => '删除',
        'update' => '修改',
        'detail' => '详情',
        'list' => '无分页列表',
        'page' => '分页列表'
    ];
    /**
     * @var string
     */
    public $authorName;
    /**
     * @var string
     */
    public $authorEmail;

    public function __construct($parameter)
    {
        $this->setConsole();
        $this->setParameter($parameter);
        $this->setAuthorInfo();
        $this->setTemplateParser();
    }

    /**
     * 构建各个子类的基础文件
     * @param $direct
     * @param $assign
     */
    public function initBuild($direct, $assign)
    {
        $path = $this->path ? '\\'.ucfirst($this->path) : '';
        // 追加公共字段
        $assign = array_merge($assign, [
            'AUTHOR' => $this->getAuthorContent(),
            'CLASS_NAME' => $this->getClassName(),
            'PATH'       => $path
        ]);
        // 获取模板
        $template = $this->getBasicTemplate();
        // 注入模板
        $fileContent = $this->templateParser->assign($assign, $template);
        // 生成文件
        $this->templateParser->buildFile($this->getDocumentDirectPrefix(), $direct, $fileContent);
        $this->console->info('已生成'.$this->getClassName().'基础文件');
    }

    /**
     * 配置参数
     * @param $parameter
     */
    private function setParameter($parameter)
    {
        $this->api = key_exists('api', $parameter) ? $parameter['api'] : '';
        $this->api = $this->getRealApiName();
        if (key_exists('table', $parameter)){
            $arr = explode('#', $parameter['table']);
            $this->rename = isset($arr[1]) ? $arr[1] : $arr[0];
            $this->table  = $arr[0];
        }
        $this->pre = key_exists('pre', $parameter) ? $parameter['pre'] : '';
        $this->path = $parameter['path'];
    }

    /**
     * 引入模板引擎
     */
    private function setTemplateParser()
    {
        $this->templateParser = new TemplateParser();
    }

    /**
     * 引入输出引擎
     */
    private function setConsole()
    {
        $this->console = new Console();
    }

    /**
     * 配置用户名称信息
     */
    private function setAuthorInfo()
    {
        $nameShell = 'git config --get user.name ';
        $emailShell = 'git config --get user.email';
        $name = shell_exec($nameShell);
        $email = shell_exec($emailShell);
        if ($name) {
            $this->authorName = str_replace(PHP_EOL, '', $name);
        } else {
            $this->authorName = 'developer';
        }
        if ($email) {
            $this->authorEmail = str_replace(PHP_EOL, '', $email);
        } else {
            $this->authorEmail = 'developer@uniondrug.cn';
        }
    }

    /**
     * 读取作者信息
     * @return string
     */
    protected function getAuthorContent()
    {
        $author = '/**'.PHP_EOL;
        $author .= ' * Created by Builder'.PHP_EOL;
        $author .= ' * @Author '.$this->authorName.' <'.$this->authorEmail.'>'.PHP_EOL;
        $author .= ' * @Date   '.date('Y-m-d').PHP_EOL;
        $author .= ' * @Time   '.date('H:i:s').PHP_EOL;
        $author .= ' */';
        return $author;
    }

    /**
     * @return string
     */
    protected function _tableName()
    {
        $nameArr = explode('_', strtolower($this->rename));
        $tableName = '';
        foreach ($nameArr as $value) {
            $tableName .= ucfirst($value);
        }
        return $tableName;
    }

    /**
     * 获取类名
     * @return string
     */
    protected function getClassName()
    {
        $className = $this->_tableName();
        $apiName = $this->api ? ucfirst($this->api) : '';
        switch ($this->classType) {
            case 'Controller':
                $className = $className.'Controller';
                break;
            case 'Service':
                $className = $className.'Service';
                break;
            case 'Model':
                $className = $className.'';
                break;
            case 'Trait':
                $className = $className.'Trait';
                break;
            case 'Logic':
                $className = $apiName.'Logic';
                break;
            case 'Request':
                $className = $apiName.'Request';
                break;
            case 'Result':
                $className = $apiName.'Result';
                break;
            default:
                $className = '';
                break;
        }
        return $className;
    }

    /**
     * 查询字段的Validator类型
     * @param $type
     * @return string
     */
    protected function getType($type)
    {
        switch ($type) {
            case in_array($type, $this->int):
            case in_array($type, $this->timestamp):
                return 'int';
                break;
            case in_array($type, $this->string):
            case in_array($type, $this->time):
                return 'string';
                break;
            case in_array($type, $this->float):
                return 'float';
                break;
            default:
                return 'string';
                break;
        }
    }

    /**
     * 属性列表
     * @param $columns
     * @return string
     */
    protected function getPropertyContent($columns)
    {
        $longestNum = $this->getLongestCharNum($columns);
        $longestTypeNum = $this->getTypeCharNum($columns);
        $propertyTemplateContent = [];
        foreach ($columns as $key => $value) {
            $propertyTemplateContentString = ' * @property ';
            $firstlen = $longestTypeNum - strlen($this->getType($value['dataType'])) + 1;
            $propertyTemplateContentString .= $this->getType($value['dataType']).str_repeat(' ', $firstlen);
            $secendlen = $longestNum - strlen($value['camelColumnName']) + 1;
            $propertyTemplateContentString .= '$'.$value['camelColumnName'].str_repeat(' ', $secendlen);
            $propertyTemplateContentString .= ' '.$value['columnComment'];
            $propertyTemplateContent[] = $propertyTemplateContentString;
        }
        return implode(PHP_EOL, $propertyTemplateContent);
    }

    /**
     * 获取最长的字段的字符数
     * @param $columns
     * @return int
     */
    protected function getLongestCharNum($columns)
    {
        $longestNum = 0;
        foreach ($columns as $column) {
            if (strlen($column['camelColumnName']) > $longestNum) {
                $longestNum = strlen($column['camelColumnName']);
            }
        }
        return $longestNum;
    }

    /**
     * 获取类型最长的字符数
     * @param $columns
     * @return int
     */
    protected function getTypeCharNum($columns)
    {
        $longestTypeNum = 0;
        foreach ($columns as $column) {
            if (strlen($this->getType($column['dataType'])) > $longestTypeNum) {
                $longestTypeNum = strlen($this->getType($column['dataType']));
            }
        }
        return $longestTypeNum;
    }

    /**
     * 获取文件名
     * @param int $row
     * @return string
     */
    protected function getFileName($row = 0)
    {
        if ($row) {
            return 'RowResult.php';
        }
        $tableName = $this->_tableName();
        $api = $this->api ? ucfirst($this->api) : '';
        switch ($this->classType) {
            case 'Model':
                return $tableName.'.php';
                break;
            case 'Trait':
                return $tableName.'Trait.php';
                break;
            case 'Controller':
                return $tableName.'Controller.php';
                break;
            case 'Service':
                return $tableName.'Service.php';
                break;
            case 'Logic':
                return $api.'Logic.php';
                break;
            case 'Request':
                return $api.'Request.php';
                break;
            case 'Result':
                return $api.'Result.php';
                break;
            default:
                return '';
        }
    }

    /**
     * 获取文件名
     * @param int $row
     * @return string
     */
    protected function getOldFileName($row = 0)
    {
        if ($row) {
            return 'Row.php';
        }
        $tableName = $this->_tableName();
        $api = $this->api ? ucfirst($this->api) : '';
        switch ($this->classType) {
            case 'Model':
                return $tableName.'.php';
                break;
            case 'Trait':
                return $tableName.'Trait.php';
                break;
            case 'Controller':
                return $tableName.'Controller.php';
                break;
            case 'Service':
                return $tableName.'Service.php';
                break;
            case 'Logic':
                return $api.'Logic.php';
                break;
            case 'Request':
                return $api.'Request.php';
                break;
            case 'Result':
                return $api.'Result.php';
                break;
            default:
                return '';
        }
    }

    /**
     * @param $type
     * @param $column
     * @return string
     */
    protected function getValidator($type, $column)
    {
        $validator = [];
        if ($column['isNullAble'] == "NO") {
            $validator[] = 'required';
        }
        if ($type == 'string' && $column['characterMaximumLength']) {
            $validator[] = 'options={minChar:1,maxChar:'.$column['characterMaximumLength'].'}';
        }
        if (!isset($validator)) {
            return '';
        }
        return implode(',', $validator);
    }

    /**
     * 获取文件对应的目录
     * @return string
     */
    protected function getDocumentDirectPrefix()
    {
        $prefix = '';
        $tableName = $this->_tableName();
        $base = './app/';
        $path =  $this->path ? ucfirst($this->path).'/' : '';
        switch ($this->classType) {
            case 'Controller':
                $prefix = $base.'Controllers/'.$path;
                break;
            case 'Service':
                $prefix = $base.'Services/';
                break;
            case 'Model':
                $prefix = $base.'Models/';
                break;
            case 'Trait':
                $prefix = $base.'Structs/Traits/';
                break;
            case 'Logic':
                $prefix = $base.'Logics/'.$tableName.'/';
                break;
            case 'Request':
                $prefix = $base.'Structs/Requests/'.$path.$tableName.'/';
                break;
            case 'Result':
                $prefix = $base.'Structs/Results/'.$path.$tableName.'/';
                break;
        }
        return $prefix;
    }

    /**
     * 获取文件对应的基础模板
     * @return bool|string
     */
    protected function getBasicTemplate()
    {
        $templateDirect = './vendor/drcarpen/builder/src/Components/Template/Basic/';
        switch ($this->classType) {
            case 'Controller':
                $templateDirect = $templateDirect.'BasicController.template';
                break;
            case 'Service':
                $templateDirect = $templateDirect.'BasicService.template';
                break;
            case 'Model':
                $templateDirect = $templateDirect.'BasicModel.template';
                break;
            case 'Trait':
                $templateDirect = $templateDirect.'BasicTrait.template';
                break;
            case 'Logic':
                $templateDirect = $templateDirect.'BasicLogic.template';
                break;
            case 'Request':
                $templateDirect = $templateDirect.'BasicRequest.template';
                break;
            case 'Result':
                $templateDirect = $templateDirect.'BasicResult.template';
                break;
        }
        return file_get_contents($templateDirect);
    }

    /**
     * 获取分部模板
     * @param string $partTemplate
     * @return bool|string
     */
    protected function getPartTemplate($partTemplate = '')
    {
        $templateDirect = './vendor/drcarpen/builder/src/Components/Template/Part/';
        switch ($this->classType) {
            case 'Controller':
                $templateDirect = $templateDirect.'ControllerPart.template';
                break;
            case 'Service':
                switch ($partTemplate){
                    case 'create':
                    case 'c':
                        $templateDirect = $templateDirect.'ServiceCreatePart.template';
                        break;
                    case 'update':
                    case 'u':
                        $templateDirect = $templateDirect.'ServiceUpdatePart.template';
                        break;
                    case 'delete':
                    case 'd':
                        $templateDirect = $templateDirect.'ServiceDeletePart.template';
                        break;
                    case 'r':
                    case 'detail':
                        $templateDirect = $templateDirect.'ServiceDetailPart.template';
                        break;
                    case 'l':
                    case 'list':
                    case 'listing':
                        $templateDirect = $templateDirect.'ServiceListingPart.template';
                        break;
                    case 'p':
                    case 'page':
                    case 'paging':
                        $templateDirect = $templateDirect.'ServicePagingPart.template';
                        break;
                    default:
                        $templateDirect = $templateDirect.'ServicePart.template';
                }
                break;
            case 'Model':
                if (!$partTemplate) {
                    $templateDirect = $templateDirect.'ModelPart.template';
                } else if ($partTemplate == 'ModeColumnMap') {
                    $templateDirect = $templateDirect.'ModelColumnMapPart.template';
                } else if ($partTemplate == 'ModeConstant') {
                    $templateDirect = $templateDirect.'ModelConstantPart.template';
                } else if ($partTemplate == 'ModeTextFunc') {
                    $templateDirect = $templateDirect.'ModelTextFuncPart.template';
                } else if ($partTemplate == 'ModeTextArray') {
                    $templateDirect = $templateDirect.'ModelTextArrayPart.template';
                } else if ($partTemplate == 'ModeText') {
                    $templateDirect = $templateDirect.'ModelTextPart.template';
                } else if ($partTemplate == 'ModelInitialize') {
                    $templateDirect = $templateDirect.'ModelInitializePart.template';
                }
                break;
            case 'Trait':
                $templateDirect = $templateDirect.'TraitPart.template';
                break;
            case 'Logic':
                $templateDirect = $templateDirect.'LogicPart.template';
                break;
            case 'Request':
                $templateDirect = $templateDirect.'RequestPart.template';
                break;
            case 'Result':
                $templateDirect = $templateDirect.'ResultPart.template';
                break;
        }
        return file_get_contents($templateDirect);
    }

    /**
     * 读取文件
     * @param $direct
     * @return bool|string
     */
    protected function getInitFile($direct)
    {
        return file_get_contents($direct);
    }

    /**
     * 覆盖文件
     * @param $fileDirect
     * @param $file
     */
    protected function rewriteFile($fileDirect, $file)
    {
        file_put_contents($fileDirect, $file);
    }

    /**
     * 检查文件是否存在
     * @param $direct
     * @return bool
     */
    protected function checkFileExsit($direct)
    {
        if (file_exists($direct)) {
            return true;
        }
        return false;
    }

    /**
     * api入参简写对照
     * @return string
     */
    protected function getRealApiName()
    {
        switch ($this->api){
            case 'c':
                $api = 'create';break;
            case 'u':
                $api = 'update';break;
            case 'r':
                $api = 'detail';break;
            case 'd':
                $api = 'delete';break;
            case 'l':
                $api = 'listing';break;
            case 'p':
                $api = 'paging';break;
            default:
                $api = $this->api;
        }
        return $api;
    }
}