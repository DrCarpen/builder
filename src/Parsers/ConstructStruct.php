<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-06
 */
namespace Uniondrug\Builder\Parsers;

use Uniondrug\Builder\Parsers\Abstracts\Construct;

class ConstructStruct extends Construct
{
    protected $fileType = 'struct';
    private $columns;
    private $html = [
        'create',
        'delete',
        'update',
        'detail',
        'listing',
        'paging'
    ];

    public function __construct($dbConfig, $authorConfig)
    {
        parent::__construct($dbConfig, $authorConfig);
    }

    /**
     * @param $columns
     */
    public function build($columns)
    {
        $this->columns = $columns;
        $this->getFileContent();
        $this->buildFile($this->html);
    }

    /**
     * 获取内容
     * @return string
     */
    private function getFileContent()
    {
        foreach ($this->html as $key => $value) {
            $html = $this->getStruct($this->getAuthorInfo(), ucfirst($value));
            $this->html[$value] = $html;
        }
    }

    /**
     * 配置头文件
     * @param $author
     * @param $structHead
     * @return string
     */
    private function getStruct($author, $structHead)
    {
        $template = <<<'TEMP'
<?php
{{AUTHOR}}
namespace App\Structs\Requests\{{CLASS_NAME}};
        
use Uniondrug\Structs\{{STRUCT_NAME}};

class {{STRUCT_HEAD}}Struct extends {{STRUCT_NAME}}
{
{{STRUCT_BODY}}
}

TEMP;
        $replaceList = [
            'AUTHOR' => $author,
            'CLASS_NAME' => $this->className,
            'STRUCT_HEAD' => $structHead,
            'STRUCT_NAME' => $structHead == 'Paging' ? 'PagingRequest' : 'Struct',
            'STRUCT_BODY' => !in_array($structHead, [
                'Delete',
                'Detail'
            ]) ? $this->getCreateStruct() : ''
        ];
        return $this->templeteParser->repalceTempale($replaceList, $template);
    }

    /**
     * 新增接口的
     * @return string
     */
    private function getCreateStruct()
    {
        $template = <<<'TEMP'
    /**
     * {{COLUMN_COMMENT}}
     * @var {{DATA_TYPE}}
     * @validator({{VALIDATOR_TYPE}})
     */
    public ${{COLUMN_NAME}};
TEMP;
        $templateList = '';
        foreach ($this->columns as $key => $value) {
            if (!in_array($value['COLUMN_NAME'], $this->noShowFields) && $value['COLUMN_KEY'] != 'PRI' && !in_array($value['COLUMN_NAME'], [
                    'gmtCreated',
                    'gmtUpdated'
                ])) {
                $repalceList = [
                    'COLUMN_COMMENT' => $value['COLUMN_COMMENT'],
                    'VALIDATOR_TYPE' => $this->getValidator($this->getType($value['DATA_TYPE']), $value),
                    'DATA_TYPE' => $this->getType($value['DATA_TYPE']),
                    'COLUMN_NAME' => $value['COLUMN_NAME']
                ];
                $templateList .= $this->templeteParser->repalceTempale($repalceList, $template);
            }
        }
        return $templateList;
    }
}
