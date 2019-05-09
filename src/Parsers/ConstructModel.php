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
        $html = $this->getHead($this->getAuthorInfo());
        $html .= $this->getProperty();
        $html .= $this->getBottom();
        return $html;
    }

    /**
     * 获取注释
     * @return string
     */
    private function getProperty()
    {
        $property = '/**'.PHP_EOL;
        foreach ($this->columns as $key => $value) {
            if (!in_array($value['COLUMN_NAME'], $this->noShowFields)) {
                $property .= ' * @property '.$this->getType($value['DATA_TYPE']).'      $'.$value['COLUMN_NAME'].'     '.$value['COLUMN_COMMENT'].PHP_EOL;
            }
            if ($value['COLUMN_NAME'] == 'status') {
                $this->isHasStatus = true;
            }
        }
        $property .= ' * @package App\Models'.PHP_EOL;
        $property .= ' */'.PHP_EOL;
        return $property;
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
namespace App\Models;

use App\Models\Abstracts\Model;


TEMP;
        return $this->templeteParser->repalceTempale(['AUTHOR' => $author], $template);
    }

    /**
     * 配置底部文件
     * @return string
     */
    private function getBottom()
    {
        $template = <<<'TEMP'
class {{CLASS_NAME}} extends Model
{
{{STATUS_TEXT}}
    public function getSource()
    {
        return "{{TABLE}}";
    }
}
    
TEMP;
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
        $template = $this->templeteParser->repalceTempale(['STATUS_TEXT' => ($this->isHasStatus ? $statusText : '')], $template);
        $replaceList = [
            'TABLE' => $this->table,
            'CLASS_NAME' => $this->className
        ];
        return $this->templeteParser->repalceTempale($replaceList, $template);
    }
}