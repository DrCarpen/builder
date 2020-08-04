<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-06
 */
namespace Uniondrug\Builder\Components\Build;

use Uniondrug\Builder\Components\Build\BuildBasic;

class BuildModel extends BuildBasic
{
    public function __construct($parameter)
    {
        parent::__construct($parameter);
        $this->classType = 'Model';
    }

    /**
     * @param $columns
     * @return bool
     */
    public function build($columns)
    {
        // 获取文件名称
        $direct = $this->getDocumentDirectPrefix().$this->getFileName();
        $oldDirect = $this->getDocumentDirectPrefix().$this->getOldFileName();
        // 判断目录是否存在
        if ($this->checkFileExsit($direct) || $this->checkFileExsit($oldDirect)) {
            $this->console->warning('Model文件已存在！');
            return false;
        }
        $init = [
            'COLUMN_MAP_LIST' => $this->getColumnMap($columns),
            'PROPERTY_TEMPLATE_LIST' => $this->getPropertyContent($columns)
        ];
        // 注解列表
        $this->initBuild($direct, $init);
        return true;
    }
}