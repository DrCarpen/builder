<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-09
 */
namespace Uniondrug\Builder\Tools;

/**
 * Class TemplateParser
 * @package Uniondrug\Builder\Tools
 */
class TemplateParser
{
    public function repalceTempale($replaceList, $template)
    {
        if (!empty($replaceList)) {
            foreach ($replaceList as $key => $value) {
                $rexp = '/\{\{'.$key.'\}\}/';
                $template = preg_replace($rexp, $value, $template);
            }
        }
        return $template.PHP_EOL;
    }
}