<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-06
 */
namespace Uniondrug\Builder\Components\Build;

class BuildLogic extends Base
{
    /**
     * BuildLogic constructor.
     * @param $parameter
     */
    public function __construct($parameter)
    {
        parent::__construct($parameter);
        $this->classType = 'Logic';
    }

    /**
     * @param $columns
     * @return bool
     */
    public function build($columns)
    {
        // 获取文件名称
        $direct = $this->getDocumentDirectPrefix().$this->getFileName();
        // 判断基础文件是否存在
        if (!$this->checkFileExsit($direct)) {
            $this->initBuild($direct, [
                'TABLE_NAME' => $this->_tableName(),
                'MIN_TABLE_NAME' => lcfirst($this->_tableName()),
                'MAX_API' => ucfirst($this->api),
                'MIN_API' => $this->api,
            ]);
        }
        return true;
    }
}