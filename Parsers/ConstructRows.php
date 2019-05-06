<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-06
 */

class ConstructRows extends Construct
{
    protected $fileType = 'rows';

    public function __construct($dbConfig, $authorConfig)
    {
        parent::__construct($dbConfig, $authorConfig);
    }

    public function build()
    {
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
        $head .= 'namespace App\Structs\Results\\'.$this->className.';'.PHP_EOL.PHP_EOL;
        $head .= 'use Uniondrug\Structs\PaginatorStruct;'.PHP_EOL.PHP_EOL;
        return $head;
    }

    /**
     * 获取注释
     * @return string
     */
    private function getProperty()
    {
        $property = '/**'.PHP_EOL;
        $property .= ' * Class Rows'.PHP_EOL;
        $property .= ' * @package App\Structs\Results\\'.$this->className.PHP_EOL;
        $property .= ' */'.PHP_EOL;
        $property .= 'class Rows extends PaginatorStruct'.PHP_EOL;
        $property .= '{'.PHP_EOL;
        $property .= '    /**'.PHP_EOL;
        $property .= '     * @var Row[]'.PHP_EOL;
        $property .= '     */'.PHP_EOL;
        $property .= '    public $body;'.PHP_EOL;
        $property .= '}'.PHP_EOL;
        return $property;
    }
}