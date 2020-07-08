<?php
/**
 * Created by PhpStorm.
 * User: liyang
 * Date: 2020/7/7
 * Time: 4:29 PM
 */
namespace Uniondrug\Builder\Modes;

/**
 * 简单模式
 * Class SimpleMode
 * @package Uniondrug\Builder\Modes
 */
class SimpleMode
{
    public $dbConfig;
    public $authorConfig;
    public $base;

    public function __construct($base, $dbConfig, $authorConfig)
    {
        parent::__construct();
        $this->base = $base;
        $this->dbConfig = $dbConfig;
        $this->authorConfig = $authorConfig;
    }

    public function build()
    {
        $this->console->info('开始初始化Model');
        $model = new Model($this->dbConfig);
        $columns = $model->build();
    }
}