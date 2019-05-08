<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-06
 */
namespace Uniondrug\Builder\Parsers;

use Uniondrug\Builder\Parsers\Abstracts\Base;
use Uniondrug\Builder\Parsers\Abstracts\Model;
use Uniondrug\Builder\Parsers\ConstructModel;
use Uniondrug\Builder\Parsers\ConstructTrait;
use Uniondrug\Builder\Parsers\ConstructRow;
use Uniondrug\Builder\Parsers\ConstructRows;
use Uniondrug\Builder\Parsers\ConstructController;
use Uniondrug\Builder\Parsers\ConstructListing;
use Uniondrug\Builder\Parsers\ConstructService;
use Uniondrug\Builder\Parsers\ConstructLogic;
use Uniondrug\Builder\Parsers\ConstructStruct;

class Collection extends Base
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
        $this->console->info('数据库查询成功');
        $this->console->info('开始构造Model文件');
        $constructModel = new ConstructModel($this->dbConfig, $this->authorConfig);
        $constructModel->build($columns);
        $this->console->info('开始构造Trait');
        $constructTrait = new ConstructTrait($this->dbConfig, $this->authorConfig);
        $constructTrait->build($columns);
        $this->console->info('开始构造Row文件');
        $constructRow = new ConstructRow($this->dbConfig, $this->authorConfig);
        $constructRow->build();
        $this->console->info('开始构造Rows文件');
        $constructRows = new ConstructRows($this->dbConfig, $this->authorConfig);
        $constructRows->build();
        $this->console->info('开始构造Listing文件');
        $constructController = new ConstructController($this->dbConfig, $this->authorConfig);
        $constructController->build();
        $this->console->info('开始构造Model文件');
        $constructListing = new ConstructListing($this->dbConfig, $this->authorConfig);
        $constructListing->build();
        $this->console->info('开始构造Service文件');
        $constructService = new ConstructService($this->dbConfig, $this->authorConfig);
        $constructService->build();
        $this->console->info('开始构造Logic文件');
        $constructLogic = new ConstructLogic($this->dbConfig, $this->authorConfig);
        $constructLogic->build();
        $this->console->info('开始构造构造Struct文件');
        $constructStruct = new ConstructStruct($this->dbConfig, $this->authorConfig);
        $constructStruct->build($columns);
        $this->console->info('处理完毕！！！');
    }
}
