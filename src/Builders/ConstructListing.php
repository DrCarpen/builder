<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-06
 */
namespace Uniondrug\Builder\Parsers;

use Uniondrug\Builder\Parsers\Abstracts\Construct;

class ConstructListing extends Construct
{
    protected $fileType = 'listing';

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
        return $html;
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
 
use Uniondrug\Structs\Struct;

/**
 * Class Listing
 * @package App\Structs\Results\{{CLASS_NAME}}
 */
 class Listing extends Struct
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