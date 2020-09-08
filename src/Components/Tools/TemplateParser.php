<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-09
 */
namespace Uniondrug\Builder\Components\Tools;

/**
 * Class TemplateParser
 * @package Uniondrug\Builder\Components\Tools
 */
class TemplateParser
{
    /**
     * 字段注入模板
     * @param $replaceList
     * @param $template
     * @return null|string|string[]
     */
    public function assign($replaceList, $template)
    {
        if (!empty($replaceList)) {
            foreach ($replaceList as $key => $value) {
                $rexp = '/\{\{'.$key.'\}\}/';
                $template = preg_replace($rexp, $value, $template);
            }
        }
        return $template;
    }

    /**
     * 创建文件
     * @param $pathPrifix
     * @param $fileDirect
     * @param $content
     */
    public function buildFile($pathPrifix, $fileDirect, $content)
    {
        if (!is_dir($pathPrifix)) {
            mkdir($pathPrifix, 0777, true);
        }
        file_put_contents($path, $content);
    }
}