<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-06
 */

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
                $property .= '* @property '.$this->getType($value['DATA_TYPE']).'      $'.$value['COLUMN_NAME'].'     '.$value['COLUMN_COMMENT'].PHP_EOL;
            }
            if ($value['COLUMN_NAME'] == 'status') {
                $this->isHasStatus = true;
            }
        }
        $property .= '* @package App\Models'.PHP_EOL.'*/'.PHP_EOL;
        return $property;
    }

    /**
     * 配置头文件
     * @param $author
     * @return string
     */
    private function getHead($author)
    {
        $head = '<?php'.PHP_EOL;
        $head .= $author;
        $head .= 'namespace App\Models;'.PHP_EOL.PHP_EOL;
        $head .= 'use App\Models\Abstracts\Model;'.PHP_EOL.PHP_EOL;
        return $head;
    }

    /**
     * 配置底部文件
     * @return string
     */
    private function getBottom()
    {
        $bottom = 'class '.$this->className.' extends Model
{
    public function getSource()
    {
        return "'.$this->table.'";
    }';
        if ($this->isHasStatus) {
            $bottom .= '
    const STATUS_ON = 1;
    const STATUS_OFF = 0;
    private static $_statusText = [
        self::STATUS_ON => \'已开启\',
        self::STATUS_OFF => \'已关闭\'
    ];
    private static $_unknowsMessage = \'非法状态\';

   

    public function getStatusText()
    {
        return isset(static::$_statusText[$this->status]) ? static::$_statusText[$this->status] : static::$_unknowsMessage;
    }
';
        }
        $bottom .= PHP_EOL.'}';
        return $bottom;
    }
}