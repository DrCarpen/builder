<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-06
 */
namespace Uniondrug\Builder\Parsers;

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
            $html = $this->getHead($this->getAuthorInfo(), ucfirst($value));
            $html .= $this->getProperty(ucfirst($value));
            $html .= $this->getBody(ucfirst($value));
            $html .= $this->getBottom();
            $this->html[$value] = $html;
        }
    }

    /**
     * 配置头文件
     * @param $author
     * @param $structHead
     * @return string
     */
    private function getHead($author, $structHead)
    {
        $head = '<?php'.PHP_EOL;
        $head .= $author;
        $head .= 'namespace App\Logics\\'.$this->className.';'.PHP_EOL.PHP_EOL;
        $head .= 'use App\Logics\Abstracts\Logic;'.PHP_EOL;
        $head .= 'use App\Structs\Requests\\'.$this->className.'\\'.$structHead.'Struct;'.PHP_EOL;
        if (in_array($structHead, [
            'Paging',
            'Listing'
        ])) {
            $head .= 'use App\Structs\Results\\'.$this->className.'\Rows;'.PHP_EOL;
        } else {
            $head .= 'use App\Structs\Results\\'.$this->className.'\Row;'.PHP_EOL;
        }
        $head .= PHP_EOL;
        return $head;
    }

    /**
     * 获取注释
     * @param $structHead
     * @return string
     */
    private function getProperty($structHead)
    {
        $property = 'class '.$structHead.'Logic extends Logic'.PHP_EOL;
        return $property;
    }

    /**
     * @param $structHead
     * @return string
     */
    private function getBody($structHead)
    {
        $body = '{'.PHP_EOL;
        $body .= '    public function run($payload)'.PHP_EOL;
        $body .= '    {'.PHP_EOL;
        $body .= '        $struct = '.$structHead.'Struct::factory($payload);'.PHP_EOL;
        $body .= '        $output = $this->'.strtolower($this->className).'Service->'.strtolower($structHead).'($struct);'.PHP_EOL;
        if (in_array($structHead, [
            'Paging',
            'Listing'
        ])) {
            $body .= '        return Rows::factory($output);'.PHP_EOL;
        } else {
            $body .= '        return Row::factory($output);'.PHP_EOL;
        }
        $body .= '    }'.PHP_EOL;
        $body .= PHP_EOL;
        return $body;
    }

    private function getBottom()
    {
        return '}'.PHP_EOL;
    }
}