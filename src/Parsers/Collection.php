<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-06
 */
namespace Uniondrug\Builder\Parsers;

class Collection
{
    public $dbConfig;
    public $authorConfig;
    public $base;

    public function __construct($base)
    {
        $this->base = $base;
        $this->dbConfig = require $base.'/vendor/uniondrug/MysqlDocs/Config/Db.php';
        $this->dbConfig['table'] = $this->getArgvs() ? $this->getArgvs() : $this->dbConfig['table'];
        $this->authorConfig = require $base.'/vendor/uniondrug/MysqlDocs/Config/Author.php';
    }

    public function build()
    {
        $this->formPrint('[开始初始化Model]');
        $model = new Model($this->dbConfig);
        $columns = $model->build();
        $this->formPrint('[查询数据库成功]');
        $constructModel = new ConstructModel($this->dbConfig, $this->authorConfig);
        $constructModel->build($columns);
        $this->formPrint('[构造Model]');
        $constructTrait = new ConstructTrait($this->dbConfig, $this->authorConfig);
        $constructTrait->build($columns);
        $this->formPrint('[构造Trait]');
        $constructRow = new ConstructRow($this->dbConfig, $this->authorConfig);
        $constructRow->build();
        $this->formPrint('[构造Row]');
        $constructRows = new ConstructRows($this->dbConfig, $this->authorConfig);
        $constructRows->build();
        $this->formPrint('[构造Rows]');
        $constructController = new ConstructController($this->dbConfig, $this->authorConfig);
        $constructController->build();
        $this->formPrint('[构造Model]');
        $constructService = new ConstructService($this->dbConfig, $this->authorConfig);
        $constructService->build();
        $this->formPrint('[构造Service]');
        $constructLogic = new ConstructLogic($this->dbConfig, $this->authorConfig);
        $constructLogic->build();
        $this->formPrint('[构造Logic]');
        $constructStruct = new ConstructStruct($this->dbConfig, $this->authorConfig);
        $constructStruct->build($columns);
        $this->formPrint('[构造Struct]');
        $this->formPrint('[success !! ]');
    }

    /**
     * 处理入参
     */
    private function getArgvs()
    {
        $argvs = $_SERVER['argv'];
        if (count($argvs) > 1) {
            return $argvs[1];
        }
    }

    private function formPrint($notice)
    {
        echo $notice.PHP_EOL;
    }
}

$base = getcwd();
//echo $base;die;
$init = new Document($base);
$init->build();