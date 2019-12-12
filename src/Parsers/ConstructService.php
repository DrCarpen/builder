<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-06
 */
namespace Uniondrug\Builder\Parsers;

use Uniondrug\Builder\Parsers\Abstracts\Construct;
use App\Services\Abstracts\ServiceTrait;

class ConstructService extends Construct
{
    protected $fileType = 'service';

    public function __construct($dbConfig, $authorConfig)
    {
        parent::__construct($dbConfig, $authorConfig);
    }

    /**
     * 核心方法
     */
    public function build()
    {
        $html = $this->getFileContent();
        $this->buildFile($html);
        $this->editServiceTrait();
    }

    /**
     * 获取内容
     * @return string
     */
    private function getFileContent()
    {
        $html = $this->getBody($this->getAuthorInfo());
        return $html;
    }

    /**
     * 配置头文件
     * @param $author
     * @return string
     */
    private function getBody($author)
    {
        $template = <<<'TEMP'
<?php
{{AUTHOR}}
namespace App\Services;

use App\Services\Abstracts\Service;
{{STRUCT_BODY}}

/**
 * Class {{CLASS_NAME}}Service
 * @package App\Services    
 */ 
class {{CLASS_NAME}}Service extends Service
{
{{SERVICE_BODY}}    
}

TEMP;
        return $this->templeteParser->repalceTempale([
            'AUTHOR' => $author,
            'STRUCT_BODY' => $this->getStructBody(),
            'SERVICE_BODY' => $this->getServiceBody(),
            'CLASS_NAME' => $this->className
        ], $template);
    }

    private function getServiceBody()
    {
        $template = <<<'TEMP'
    public function {{NAME}}({{UCFIRST_NAME}}Struct $struct)
    {
    }
    
TEMP;
        $templateList = '';
        foreach ($this->CURD as $key => $value) {
            $templateList .= $this->templeteParser->repalceTempale([
                'NAME' => $key,
                'UCFIRST_NAME' => ucfirst($key)
            ], $template);
        }
        return $templateList;
    }

    private function getStructBody()
    {
        $template = <<<'TEMP'
use App\Structs\Requests\{{CLASS_NAME}}\{{UCFIRST_NAME}}Struct;
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
    
    //增加改写ServiceTrait
    private function editServiceTrait()
    {
        $name=$this->className."Service";
        
        $service =new \ReflectionClass(ServiceTrait::class);
        //更改注解
        $doc = $service->getDocComment();
        $text = " * @property ".$name."  ".ucfirst($name);
        $text .= PHP_EOL."*/";
        $zs = str_replace('*/',$text,$doc);

        //更改use
        $oldUseText = "namespace App\Services\Abstracts;".PHP_EOL;
        $newUseText = $oldUseText.PHP_EOL.'use App\\Services\\'.$name.';';
        
        $filename = $service->getFileName();
        $oldFlie = file_get_contents($filename);
        $newFlie = str_replace($doc,$zs,$oldFlie);
        $newFlie = str_replace($oldUseText,$newUseText,$oldFlie);
        $this->console->info($this->className." 写入 ServiceTrait");
        //this->console->info($newFlie);exit();
        file_put_contents($filename,$newFlie);
    }
}
