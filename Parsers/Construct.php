<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-06
 */

class Construct
{
    protected $table;
    protected $docs;
    protected $className;

    /**
     * 获取类名
     * @return string
     */
    protected function getClassName()
    {
        $name = strtolower($this->table);
        $nameArr = explode('_', $name);
        $className = '';
        foreach ($nameArr as $value) {
            $className .= ucfirst($value);
        }
        return $className;
    }

    /**
     * 配置目录
     * @return string
     */
    protected function getFileDir()
    {
        return $this->docs.$this->className.'.php';
    }

    /**
     * @param $dir
     * @param $html
     */
    protected function buildFile($dir, $html)
    {
        if (!is_dir($this->docs)) {
            mkdir($this->docs, 0777, true);
        }
        file_put_contents($dir, $html);
    }
}