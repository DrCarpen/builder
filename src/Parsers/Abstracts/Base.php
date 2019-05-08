<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-08
 */
namespace Uniondrug\Builder\Parsers\Abstracts;

use Uniondrug\Builder\Parsers\Tool\Console;

class Base
{
    public $console;

    public function __construct()
    {
        $this->console = new Console();
    }
}