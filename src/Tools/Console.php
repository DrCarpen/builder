<?php
/**
 * @author wsfuyibing <websearch@163.com>
 * @date   2018-05-09
 */
namespace Uniondrug\Builder\Tools;

/**
 * 控制台消息
 * Class Console
 * @package Uniondrug\Builder\Tools
 */
class Console
{
    public function errorExit($format, ... $args)
    {
        $this->error($format, ... $args);
        exit();
    }

    public function debug($format, ... $args)
    {
        $this->printer("DEBUG", 30, 47, $format, ... $args);
    }

    public function error($format, ... $args)
    {
        $this->printer("ERROR", 33, 41, $format, ... $args);
    }

    public function info($format, ... $args)
    {
        $this->printer("INFO", 0, 0, $format, ... $args);
    }

    public function warning($format, ... $args)
    {
        $this->printer("WARNING", 31, 43, $format, ... $args);
    }

    private function printer($level, $bc, $fc, $format, ... $args)
    {
        $args = is_array($args) ? $args : [];
        $message = call_user_func_array('sprintf', array_merge([$format], $args));
        echo sprintf("\033[%d;%dm[%s] %s\033[0m\n", $bc, $fc, sprintf("%5s", $level), $message);
    }
}
