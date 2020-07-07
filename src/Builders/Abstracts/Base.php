<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-08
 */
namespace Uniondrug\Builder\Parsers\Abstracts;

use Uniondrug\Builder\Parsers\Tool\Console;
use Uniondrug\Builder\Parsers\Abstracts\TemplateParser;

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