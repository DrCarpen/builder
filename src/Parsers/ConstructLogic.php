<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-06
 */
namespace Uniondrug\Builder\Parsers;

use Uniondrug\Builder\Parsers\Abstracts\Construct;

class ConstructLogic extends Construct
{
    protected $fileType = 'logic';
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
     * 核心方法
     */
    public function build()
    {
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
            $html = $this->getBody($this->getAuthorInfo(), ucfirst($value));
            $this->html[$value] = $html;
        }
    }

    /**
     * 配置头文件
     * @param $author
     * @param $structHead
     * @return string
     */
    private function getBody($author, $structHead)
    {
        $template = <<<'TEMP'
<?php
{{AUTHOR}}
namespace App\Logics\{{CLASS_NAME}};

use App\Logics\Abstracts\Logic;
use App\Structs\Requests\{{CLASS_NAME}}\{{STRUCT_HEAD}}Struct;
use App\Structs\Results\GroupManageCopy1\{{RESULT_STRUCT}};

class {{STRUCT_HEAD}}Logic extends Logic
{
    public function run($payload)
    {
        $struct = {{STRUCT_HEAD}}Struct::factory($payload);
        $output = $this->{{LCFIRST_CLASS_NAME}}Service->{{STRUCT_HEAD}}($struct);
        return {{RESULT_STRUCT}}::factory($output);
    }
}
TEMP;
        if ($structHead == 'Paging') {
            $resultStruct = 'Rows';
        } else if ($structHead == 'Listing') {
            $resultStruct = 'Listing';
        } else {
            $resultStruct = 'Row';
        }
        return $this->templeteParser->repalceTempale([
            'CLASS_NAME' => $this->className,
            'AUTHOR' => $author,
            'STRUCT_HEAD' => $structHead,
            'RESULT_STRUCT' => $resultStruct,
            'LCFIRST_CLASS_NAME' => lcfirst($this->className)
        ], $template);
    }
}