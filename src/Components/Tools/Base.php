<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-08
 */
namespace Uniondrug\Builder\Tools\Abstracts;

use Uniondrug\Builder\Tools\Console;
use Uniondrug\Builder\Tools\TemplateParser;

/**
 * Class Base
 * @package Uniondrug\Builder\Tools\Abstracts
 */
class Base
{
    public $console;
    public $templeteParser;

    public function __construct()
    {
        $this->console = new Console();
        $this->templeteParser = new TemplateParser();
    }
}