<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-06
 */

class ConstructTrait extends Construct
{
    private $columns;
    protected $fileType = 'trait';

    public function __construct($dbConfig, $authorConfig)
    {
        parent::__construct($dbConfig, $authorConfig);
    }

    public function build($columns)
    {
        $this->columns = $columns;
        $html = $this->getFileContent();
        $this->buildFile($html);
    }

    /**
     * 获取内容
     * @return string
     */
    private function getFileContent()
    {
        $html = $this->getHead($this->getAuthorInfo());
        $html .= $this->getProperty();
        return $html;
    }

    /**
     * 配置头文件
     * @param $author
     * @return string
     */
    private function getHead($author)
    {
        $head = '<?php'.PHP_EOL;
        $head .= $author;
        $head .= 'namespace App\Structs\Traits;'.PHP_EOL.PHP_EOL;
        $head .= '/**'.PHP_EOL;
        $head .= ' * @package App\Structs\Traits'.PHP_EOL;
        $head .= ' */'.PHP_EOL;
        return $head;
    }

    /**
     * 获取注释
     * @return string
     */
    private function getProperty()
    {
        $property = 'trait UserTrait'.PHP_EOL;
        $property .= '{'.PHP_EOL;
        foreach ($this->columns as $key => $value) {
            if (!in_array($value['COLUMN_NAME'], $this->noShowFields)) {
                $property .= '    /**'.PHP_EOL;
                if ($value['COLUMN_COMMENT']) {
                    $property .= '     * '.$value['COLUMN_COMMENT'].PHP_EOL;
                }
                $property .= '     * @var '.$this->getType($value['DATA_TYPE']).PHP_EOL;
                $property .= '     */'.PHP_EOL;
                $property .= '    public $'.$value['COLUMN_NAME'].';'.PHP_EOL;
            }
        }
        $property .= '}'.PHP_EOL;
        return $property;
    }
}