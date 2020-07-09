<?php
/**
 * Created by PhpStorm.
 * User: liyang
 * Date: 2020/7/8
 * Time: 11:55 PM
 */
namespace Uniondrug\Builder\Tools;

/**
 * 工具类：实例化各类工具
 * Class ToolBar
 * @package Uniondrug\Builder\Tools
 */
class ToolBar
{
    /**
     * @var Console
     */
    public $console;
    /**
     * 表名
     * @var string
     */
    public $table;
    /**
     * 接口
     * @var string
     */
    public $api;
    /**
     * 作者名称
     * @var string
     */
    public $authorName;
    /**
     * 作者Email
     * @var string
     */
    public $authorEmail;

    public function __construct()
    {
        $this->_console();
    }

    private function _console()
    {
        $this->console = new Console();
    }
}