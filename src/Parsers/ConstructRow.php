<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-06
 */
namespace Uniondrug\Builder\Parsers;

use Uniondrug\Builder\Parsers\Abstracts\Construct;

class ConstructRow extends Construct
{
    protected $fileType = 'row';

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
        $head .= 'use App\Structs\Traits\\'.$this->className.'Trait;'.PHP_EOL;
        $head .= 'use Uniondrug\Structs\Struct;'.PHP_EOL.PHP_EOL;
        return $head;
    }

    /**
     * 获取注释
     * @return string
     */
    private function getProperty()
    {
        $property = '/**'.PHP_EOL;
        $property .= ' * Class Row'.PHP_EOL;
        $property .= ' * @package App\Structs\Results\\'.$this->className.PHP_EOL;
        $property .= ' */'.PHP_EOL;
        $property .= 'class Row extends Struct'.PHP_EOL;
        $property .= '{'.PHP_EOL;
        $property .= '    use '.$this->className.'Trait;'.PHP_EOL;
        $property .= '}'.PHP_EOL;
        return $property;
    }
}