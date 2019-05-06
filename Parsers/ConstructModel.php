<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-06
 */

class ConstructModel extends Construct
{
    private $columns;
    private $noShowFields;
    private $isHasStatus = false;
    private $name;
    private $email;
    // int类型包含的子类型
    private $int = [
        'int',
        'integer',
        'tinyint',
        'smallint',
        'mediumint',
        'bigint'
    ];
    // string字符串类型包含的子类型
    private $string = [
        'char',
        'varchar',
        'text',
        'tinytext',
        'mediumtext',
        'longtext',
        'json'
    ];
    // float类型包含的子类型
    private $float = [
        'double',
        'float',
        'decimal'
    ];
    // 日期类型包含的子类型
    private $time = [
        'date',
        'datetime',
        'year',
        'time'
    ];
    // 时间戳类型
    private $timestamp = [
        'timestamp'
    ];

    public function __construct($dbConfig,$authorConfig)
    {
        $this->table = $dbConfig['table'];
        $this->name = $authorConfig['name'];
        $this->email = $authorConfig['email'];
        $this->noShowFields = $dbConfig['noShowFields'];
        $this->className = $this->getClassName();
        $this->docs = 'Docs/Model/';
    }

    public function build($columns)
    {
        $this->columns = $columns;
        $html = $this->getFileContent();
        $this->buildFile($this->getFileDir(), $html);
    }

    /**
     * 查询此类型
     * @param $type
     * @return string
     */
    private function getType($type)
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
     * 获取注释
     * @return string
     */
    private function getProperty()
    {
        $property = '/**'.PHP_EOL;
        foreach ($this->columns as $key => $value) {
            if (!in_array($value['COLUMN_NAME'], $this->noShowFields)) {
                $property .= '* @property '.$this->getType($value['DATA_TYPE']).'      $'.$value['COLUMN_NAME'].'     '.$value['COLUMN_COMMENT'].PHP_EOL;
            }
            if ($value['COLUMN_NAME'] == 'status') {
                $this->isHasStatus = true;
            }
        }
        $property .= '* @package App\Models'.PHP_EOL.'*/'.PHP_EOL;
        return $property;
    }

    /**
     * 配置头文件
     * @return string
     */
    private function getHead()
    {
        $head = '<?php'.PHP_EOL;
        $head .= '/**'.PHP_EOL;
        $head .= '* @author '.$this->name.' <'.$this->email.'>'.PHP_EOL;
        $head .= '* @date   '.date('Y-m-d').PHP_EOL;
        $head .= '*/'.PHP_EOL;
        $head .= 'namespace App\Models;'.PHP_EOL.PHP_EOL;
        $head .= 'use App\Models\Abstracts\Model;'.PHP_EOL.PHP_EOL;
        return $head;
    }

    /**
     * 配置底部文件
     * @return string
     */
    private function getBottom()
    {
        $bottom = 'class '.$this->className.' extends Model
{
    public function getSource()
    {
        return "'.$this->table.'";
    }';
        if ($this->isHasStatus) {
            $bottom .= '
    const STATUS_ON = 1;
    const STATUS_OFF = 0;
    private static $_statusText = [
        self::STATUS_ON => \'已开启\',
        self::STATUS_OFF => \'已关闭\'
    ];
    private static $_unknowsMessage = \'非法状态\';

   

    public function getStatusText()
    {
        return isset(static::$_statusText[$this->status]) ? static::$_statusText[$this->status] : static::$_unknowsMessage;
    }
';
        }
        $bottom .= PHP_EOL.'}';
        return $bottom;
    }

    /**
     * 获取内容
     * @return string
     */
    private function getFileContent()
    {
        $html = $this->getHead();
        $html .= $this->getProperty();
        $html .= $this->getBottom();
        return $html;
    }
}