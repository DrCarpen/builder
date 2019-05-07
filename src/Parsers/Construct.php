<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-06
 */
namespace Uniondrug\Builder\Parsers;

class Construct
{
    protected $table;
    protected $docs;
    protected $className;
    protected $name;
    protected $email;
    protected $noShowFields;
    protected $fileType; // 文件类型：model;trait;controller;service;logic;
    // int类型包含的子类型
    protected $int = [
        'int',
        'integer',
        'tinyint',
        'smallint',
        'mediumint',
        'bigint'
    ];
    // string字符串类型包含的子类型
    protected $string = [
        'char',
        'varchar',
        'text',
        'tinytext',
        'mediumtext',
        'longtext',
        'json'
    ];
    // float类型包含的子类型
    protected $float = [
        'double',
        'float',
        'decimal'
    ];
    // 日期类型包含的子类型
    protected $time = [
        'date',
        'datetime',
        'year',
        'time'
    ];
    // 时间戳类型
    protected $timestamp = [
        'timestamp'
    ];

    public function __construct($dbConfig, $authorConfig)
    {
        $this->name = $authorConfig['name'];
        $this->email = $authorConfig['email'];
        $this->table = $dbConfig['table'];
        $this->noShowFields = $dbConfig['noShowFields'];
        $this->className = $this->getClassName();
        $this->getDocs();
    }

    /**
     * 查询此类型
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

    protected function getValidator($type, $column)
    {
        if ($type == 'string') {
            $validator = 'options={mixChar:1,maxChar:'.$column['CHARACTER_MAXIMUM_LENGTH'].'}';
        } else {
            $validator = '';
        }
        return $validator;
    }

    /**
     * 获取类名
     * @return string
     */
    protected function getClassName()
    {
        $name = strtolower($this->table);
        $nameArr = explode('_', $name);
        $className = '';
        foreach ($nameArr as $value) {
            $className .= ucfirst($value);
        }
        return $className;
    }

    /**
     * 获取文件名及路径
     * @return string
     */
    protected function getFileDir()
    {
        switch ($this->fileType) {
            case 'model':
                return $this->docs.$this->className.'.php';
                break;
            case 'trait':
                return $this->docs.$this->className.'Trait.php';
                break;
            case 'row':
                return $this->docs.'row.php';
                break;
            case 'rows':
                return $this->docs.'rows.php';
                break;
            case 'controller':
                return $this->docs.$this->className.'Controller.php';
                break;
            case 'service':
                return $this->docs.$this->className.'Service.php';
                break;
            case 'logic':
                return [
                    'create' => $this->docs.'CreateLogic.php',
                    'delete' => $this->docs.'DeleteLogic.php',
                    'update' => $this->docs.'UpdateLogic.php',
                    'detail' => $this->docs.'DetailLogic.php',
                    'listing' => $this->docs.'ListingLogic.php',
                    'paging' => $this->docs.'PagingLogic.php'
                ];
                break;
            case 'struct':
                return [
                    'create' => $this->docs.'CreateStruct.php',
                    'delete' => $this->docs.'DeleteStruct.php',
                    'update' => $this->docs.'UpdateStruct.php',
                    'detail' => $this->docs.'DetailStruct.php',
                    'listing' => $this->docs.'ListingStruct.php',
                    'paging' => $this->docs.'PagingStruct.php'
                ];
                break;
            default:
                return $this->docs.$this->className.'.php';
        }
    }

    /**
     * 获取文件对应的目录结构
     */
    private function getDocs()
    {
        switch ($this->fileType) {
            case 'model':
                $this->docs = 'app/Models/';
                break;
            case 'trait':
                $this->docs = 'app/Structs/Traits/';
                break;
            case 'row':
            case 'rows':
                $this->docs = 'app/Structs/Results/'.$this->className.'/';
                break;
            case 'controller':
                $this->docs = 'app/Controllers/';
                break;
            case 'service':
                $this->docs = 'app/Services/';
                break;
            case 'logic':
                $this->docs = 'app/Logics/'.$this->className.'/';
                break;
            case 'struct':
                $this->docs = 'app/Structs/Requests/'.$this->className.'/';
                break;
            default:
                $this->docs = 'app/Models/';
        }
    }

    /**
     * @param $html
     */
    protected function buildFile($html)
    {
        if (!is_dir($this->docs)) {
            mkdir($this->docs, 0777, true);
        }
        if (in_array($this->fileType, [
            'logic',
            'struct'
        ])) {
            $fileDir = $this->getFileDir();
            foreach ($fileDir as $key => $value) {
                if ($html[$key]) {
                    if (!file_exists($value)) {
                        file_put_contents($value, $html[$key]);
                    } else {
                        echo '[Warning file is exist]'.$value.PHP_EOL;
                        continue;
                    }
                }
            }
        } else {
            $file = $this->getFileDir();
            if (!file_exists($file)) {
                file_put_contents($file, $html);
            } else {
                echo '[Warning file is exist] '.$file.PHP_EOL;
            }
        }
    }

    /**
     * @return string
     */
    protected function getAuthorInfo()
    {
        $author = '/**'.PHP_EOL;
        $author .= ' * @author '.$this->name.' <'.$this->email.'>'.PHP_EOL;
        $author .= ' * @date   '.date('Y-m-d').PHP_EOL;
        $author .= ' */'.PHP_EOL;
        return $author;
    }
}