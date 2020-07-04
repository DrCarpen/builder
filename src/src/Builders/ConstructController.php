<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-06
 */
namespace Uniondrug\Builder\Parsers;

use Uniondrug\Builder\Parsers\Abstracts\Construct;

class ConstructController extends Construct
{
    protected $fileType = 'controller';

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
namespace App\Controllers;

use App\Controllers\Abstracts\Base;
{{STRUCT_BODY}}

/**
 * Class {{CLASS_NAME}}Controller
 * @package App\Controllers 
 * @RoutePrefix("/{{LCFIRST_CLASS_NAME}}");   
 */ 
class {{CLASS_NAME}}Controller extends Base
{
{{CONTROLLER_BODY}}   
}
TEMP;
        return $this->templeteParser->repalceTempale([
            'AUTHOR' => $author,
            'STRUCT_BODY' => $this->getStructBody(),
            'CONTROLLER_BODY' => $this->getControllerBody(),
            'CLASS_NAME' => $this->className,
            'LCFIRST_CLASS_NAME' => lcfirst($this->className)
        ], $template);
    }

    private function getStructBody()
    {
        $template = <<<'TEMP'
use App\Logics\{{CLASS_NAME}}\{{UCFIRST_NAME}}Logic;
TEMP;
        $templateList = '';
        foreach ($this->CURD as $key => $value) {
            $templateList .= $this->templeteParser->repalceTempale([
                'CLASS_NAME' => $this->className,
                'UCFIRST_NAME' => ucfirst($key)
            ], $template);
        }
        return $templateList;
    }

    private function getControllerBody()
    {
        $template = <<<'TEMP'
    /**
     * {{CURD_VALUE}}
     * @sdk {{LCFIRST_CLASS_NAME}}{{UCFIRST_CURD_KEY}}
     * @Route("/{{CURD_KEY}}")
     * @input \App\Structs\Requests\{{CLASS_NAME}}\{{UCFIRST_CURD_KEY}}Struct
     * @output \App\Structs\Results\{{CLASS_NAME}}\{{RESULT_STRUCT}}
     */
    public function {{CURD_KEY}}Action()
    {
        $output = {{UCFIRST_CURD_KEY}}Logic::factory($this->request->getJsonRawBody());
        return $this->serviceServer->withStruct($output);
    }
    
TEMP;
        $templateList = '';
        foreach ($this->CURD as $key => $value) {
            if ($key == 'paging') {
                $resultStruct = 'Rows';
            } else if ($key == 'listing') {
                $resultStruct = 'Listing';
            } else {
                $resultStruct = 'Row';
            }
            $templateList .= $this->templeteParser->repalceTempale([
                'CURD_KEY' => $key,
                'UCFIRST_CURD_KEY' => ucfirst($key),
                'CURD_VALUE' => $value,
                'CLASS_NAME' => $this->className,
                'LCFIRST_CLASS_NAME' => lcfirst($this->className),
                'RESULT_STRUCT' => $resultStruct
            ], $template);
        }
        return $templateList;
    }
}