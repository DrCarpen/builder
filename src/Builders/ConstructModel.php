<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-06
 */
namespace Uniondrug\Builder\Parsers;

use Uniondrug\Builder\Parsers\Abstracts\Construct;

class ConstructModel extends Construct
{
    private $columns;
    private $isHasStatus = false;
    protected $fileType = 'model';

    public function __construct($dbConfig, $authorConfig)
    {
        parent::__construct($dbConfig, $authorConfig);
    }

    public function build($columns)
    {
        $this->columns = $columns;
        $html = $this->getFileContent();
        $this->buildFile($html);
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
     * 获取注释
     * @return string
     */
    private function getBody($author)
    {
        $template = <<<'TEMP'
<?php
{{AUTHOR}}
namespace App\Models;

use App\Models\Abstracts\Model;
        
/**
{{PROPERTY_TEMPLATE_LIST}} * @package App\Models
 */
class {{CLASS_NAME}} extends Model
{
{{STATUS_TEXT}}
    public function getSource()
    {
        return "{{TABLE}}";
    }
}
 
TEMP;
        return $this->templeteParser->repalceTempale([
            'PROPERTY_TEMPLATE_LIST' => $this->getPropety(),
            'AUTHOR' => $author,
            'TABLE' => $this->table,
            'CLASS_NAME' => $this->className,
            'STATUS_TEXT' => $this->isHasStatus ? $this->getStatusText() : ''
        ], $template);
    }

    /**
     * @return string
     */
    private function getPropety()
    {
        $propertyTemplate = <<<'TEMP'
 * @property {{DATA_TYPE}}  ${{COLUMN_NAME}}    {{COLUMN_COMMENT}}
TEMP;
        $propertyTemplateList = '';
        foreach ($this->columns as $key => $value) {
            if (!in_array($value['COLUMN_NAME'], $this->noShowFields)) {
                $repalceList = [
                    'DATA_TYPE' => $this->getType($value['DATA_TYPE']),
                    'COLUMN_NAME' => $value['COLUMN_NAME'],
                    'COLUMN_COMMENT' => $value['COLUMN_COMMENT']
                ];
                $propertyTemplateList .= $this->templeteParser->repalceTempale($repalceList, $propertyTemplate);
            }
            if ($value['COLUMN_NAME'] == 'status') {
                $this->isHasStatus = true;
            }
        }
        return $propertyTemplateList;
    }

    /**
     * 配置底部文件
     * @return string
     */
    private function getStatusText()
    {
        $statusText = <<<'TEMP'
    const STATUS_ON = 1;
    const STATUS_OFF = 0;
    private static $_statusText = [
        self::STATUS_ON => '已开启',
        self::STATUS_OFF => '已关闭'
    ];
    private static $_unknowsMessage = '非法状态';

    public function getStatusText()
    {
        return isset(static::$_statusText[$this->status]) ? static::$_statusText[$this->status] : static::$_unknowsMessage;
    }
    
TEMP;
        return $statusText;
    }
}