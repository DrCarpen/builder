<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-06
 */
namespace Uniondrug\Builder\Parsers;

use Uniondrug\Builder\Parsers\Abstracts\Construct;

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
        $html = $this->getFileContent($this->getAuthorInfo());
        $this->buildFile($html);
    }

    /**
     * 获取注释
     * @return string
     */
    private function getFileContent($author)
    {
        $template = <<<'TEMP'
<?php
{{AUTHOR}}
namespace App\Structs\Traits;
          
/**
 * @package App\Structs\Traits
 */
trait {{CLASS_NAME}}Trait
{
{{PROPERTY_TEMPLATE_LIST}}    
}

TEMP;
        return $this->templeteParser->repalceTempale([
            'CLASS_NAME' => $this->className,
            'PROPERTY_TEMPLATE_LIST' => $this->getPropertyTemplate(),
            'AUTHOR' => $author
        ], $template);
    }

    /**
     * @return string
     */
    private function getPropertyTemplate()
    {
        $propertyTemplate = <<<'TEMP'
    /**
     * {{COLUMN_COMMENT}}
     * @var {{DATA_TYPE}}
     */
    public ${{COLUMN_NAME}};
TEMP;
        $propertyTemplateList = '';
        foreach ($this->columns as $key => $value) {
            if (!in_array($value['COLUMN_NAME'], $this->noShowFields)) {
                $replaceList = [
                    'COLUMN_COMMENT' => $value['COLUMN_COMMENT'],
                    'DATA_TYPE' => $this->getType($value['DATA_TYPE']),
                    'COLUMN_NAME' => $value['COLUMN_NAME']
                ];
                $propertyTemplateList .= $this->templeteParser->repalceTempale($replaceList, $propertyTemplate);
            }
        }
        return $propertyTemplateList;
    }
}