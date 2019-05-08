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
        $head .= 'namespace App\Structs\Requests\\'.$this->className.';'.PHP_EOL.PHP_EOL;
        if ($structHead == 'Paging') {
            $head .= 'use Uniondrug\Structs\PagingRequest;'.PHP_EOL;
        } else {
            $head .= 'use Uniondrug\Structs\Struct;'.PHP_EOL;
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
        if ($structHead == 'Paging') {
            $property = 'class '.$structHead.'Struct extends PagingRequest'.PHP_EOL;
        } else {
            $property = 'class '.$structHead.'Struct extends Struct'.PHP_EOL;
        }
        return $property;
    }

    /**
     * @param $structHead
     * @return string
     */
    private function getBody($structHead)
    {
        $body = '{'.PHP_EOL;
        if (!in_array($structHead, [
            'Delete',
            'Detail'
        ])) {
            $body .= $this->getCreateStruct();
        }
        return $body;
    }

    /**
     * 新增接口的
     * @return string
     */
    private function getCreateStruct()
    {
        $body = '';
        foreach ($this->columns as $key => $value) {
            if (!in_array($value['COLUMN_NAME'], $this->noShowFields) && $value['COLUMN_KEY'] != 'PRI' && !in_array($value['COLUMN_NAME'], [
                    'gmtCreated',
                    'gmtUpdated'
                ])) {
                $body .= '    /**'.PHP_EOL;
                if ($value['COLUMN_COMMENT']) {
                    $body .= '     * '.$value['COLUMN_COMMENT'].PHP_EOL;
                }
                $body .= '     * @var '.$this->getType($value['DATA_TYPE']).PHP_EOL;
                $body .= '     * @validator('.$this->getValidator($this->getType($value['DATA_TYPE']), $value).')'.PHP_EOL;
                $body .= '     */'.PHP_EOL;
                $body .= '    public $'.$value['COLUMN_NAME'].';'.PHP_EOL;
            }
        }
        return $body;
    }

    private function getBottom()
    {
        return '}'.PHP_EOL;
    }
}