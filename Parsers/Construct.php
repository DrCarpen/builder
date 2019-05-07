<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-06
 */

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
     * 配置目录
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
                $this->docs = 'Docs/Model/';
                break;
            case 'trait':
                $this->docs = 'Docs/Trait/';
                break;
            case 'row':
            case 'rows':
                $this->docs = 'Docs/Results/'.$this->className.'/';
                break;
            case 'controller':
                $this->docs = 'Docs/Controllers/';
                break;
            case 'service':
                $this->docs = 'Docs/Services/';
                break;
            default:
                $this->docs = 'Docs/Model/';
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
        file_put_contents($this->getFileDir(), $html);
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