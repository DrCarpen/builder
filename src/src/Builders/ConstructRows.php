<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-06
 */
namespace Uniondrug\Builder\Parsers;

use Uniondrug\Builder\Parsers\Abstracts\Construct;

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
        return $this->getHead($this->getAuthorInfo());
    }

    /**
     * 配置头文件
     * @param $author
     * @return string
     */
    private function getHead($author)
    {
        $template = <<<'TEMP'
<?php
{{AUTHOR}}
namespace App\Structs\Results\{{CLASS_NAME}};
 
use Uniondrug\Structs\PaginatorStruct;

/**
 * Class Rows
 * @package App\Structs\Results\{{CLASS_NAME}}
 */
 class Rows extends PaginatorStruct
 {
     /**
      * @var Row[]
      */
      public $body;
 }
 
TEMP;
        return $this->templeteParser->repalceTempale([
            'CLASS_NAME' => $this->className,
            'AUTHOR' => $author
        ], $template);
    }
}