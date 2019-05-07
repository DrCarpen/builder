<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-06
 */

class ConstructController extends Construct
{
    protected $fileType = 'controller';

    public function __construct($dbConfig, $authorConfig)
    {
        parent::__construct($dbConfig, $authorConfig);
    }

    public function build()
    {
        $html = $this->getFileContent();
        $this->buildFile($html);
    }

    /**
     * 获取内容
     * @return string
     */
    private function getFileContent()
    {
        $html = $this->getHead($this->getAuthorInfo());
        $html .= $this->getProperty();
        $html .= $this->getBodyCreate();
        $html .= $this->getBodyDelete();
        $html .= $this->getBodyUpdate();
        $html .= $this->getBodyDetail();
        $html .= $this->getBodyListing();
        $html .= $this->getBodyPaging();
        $html .= $this->getBottom();
        return $html;
    }

    /**
     * 配置头文件
     * @param $author
     * @return string
     */
    private function getHead($author)
    {
        $head = '<?php'.PHP_EOL;
        $head .= $author;
        $head .= 'namespace App\Controllers;'.PHP_EOL.PHP_EOL;
        $head .= 'use App\Controllers\Abstracts\Base;'.PHP_EOL;
        $head .= 'use App\Logics\\'.$this->className.'\CreateLogic;'.PHP_EOL;
        $head .= 'use App\Logics\\'.$this->className.'\DeleteLogic;'.PHP_EOL;
        $head .= 'use App\Logics\\'.$this->className.'\UpdateLogic;'.PHP_EOL;
        $head .= 'use App\Logics\\'.$this->className.'\DetailLogic;'.PHP_EOL;
        $head .= 'use App\Logics\\'.$this->className.'\ListLogic;'.PHP_EOL;
        $head .= 'use App\Logics\\'.$this->className.'\PagingLogic;'.PHP_EOL;
        $head .= PHP_EOL;
        return $head;
    }

    /**
     * 获取注释
     * @return string
     */
    private function getProperty()
    {
        $property = '/**'.PHP_EOL;
        $property .= ' * Class '.$this->className.'Controller'.PHP_EOL;
        $property .= ' * @package App\Controllers'.PHP_EOL;
        $property .= ' * @RoutePrefix("/'.strtolower($this->className).'")'.PHP_EOL;
        $property .= ' */'.PHP_EOL;
        $property .= 'class '.$this->className.'Controller extends Base'.PHP_EOL;
        return $property;
    }

    private function getBodyCreate()
    {
        $body = '{'.PHP_EOL;
        $body .= '    /**'.PHP_EOL;
        $body .= '     * 新增'.PHP_EOL;
        $body .= '     * @sdk '.strtolower($this->className).'Create'.PHP_EOL;
        $body .= '     * @Route("/create")'.PHP_EOL;
        $body .= '     * @input \App\Structs\Requests\\'.strtolower($this->className).'\CreateStruct'.PHP_EOL;
        $body .= '     * @output \App\Structs\Results\\'.strtolower($this->className).'\Row'.PHP_EOL;
        $body .= '     */'.PHP_EOL;
        $body .= '    public function createAction()'.PHP_EOL;
        $body .= '    {'.PHP_EOL;
        $body .= '        $output = CreateLogic::factory($this->request->getJsonRawBody());'.PHP_EOL;
        $body .= '        return $this->serviceServer->withStruct($output);'.PHP_EOL;
        $body .= '    }'.PHP_EOL;
        $body .= PHP_EOL;
        return $body;
    }

    private function getBodyUpdate()
    {
        $body = '    /**'.PHP_EOL;
        $body .= '     * 修改'.PHP_EOL;
        $body .= '     * @sdk '.strtolower($this->className).'Update'.PHP_EOL;
        $body .= '     * @Route("/update")'.PHP_EOL;
        $body .= '     * @input \App\Structs\Requests\\'.strtolower($this->className).'\UpdateStruct'.PHP_EOL;
        $body .= '     * @output \App\Structs\Results\\'.strtolower($this->className).'\Row'.PHP_EOL;
        $body .= '     */'.PHP_EOL;
        $body .= '    public function updateAction()'.PHP_EOL;
        $body .= '    {'.PHP_EOL;
        $body .= '        $output = UpdateLogic::factory($this->request->getJsonRawBody());'.PHP_EOL;
        $body .= '        return $this->serviceServer->withStruct($output);'.PHP_EOL;
        $body .= '    }'.PHP_EOL;
        $body .= PHP_EOL;
        return $body;
    }

    private function getBodyDelete()
    {
        $body = '    /**'.PHP_EOL;
        $body .= '     * 删除'.PHP_EOL;
        $body .= '     * @sdk '.strtolower($this->className).'Delete'.PHP_EOL;
        $body .= '     * @Route("/delete")'.PHP_EOL;
        $body .= '     * @input \App\Structs\Requests\\'.strtolower($this->className).'\DeleteStruct'.PHP_EOL;
        $body .= '     * @output \App\Structs\Results\\'.strtolower($this->className).'\Row'.PHP_EOL;
        $body .= '     */'.PHP_EOL;
        $body .= '    public function deleteAction()'.PHP_EOL;
        $body .= '    {'.PHP_EOL;
        $body .= '        $output = DeleteLogic::factory($this->request->getJsonRawBody());'.PHP_EOL;
        $body .= '        return $this->serviceServer->withStruct($output);'.PHP_EOL;
        $body .= '    }'.PHP_EOL;
        $body .= PHP_EOL;
        return $body;
    }

    private function getBodyDetail()
    {
        $body = '    /**'.PHP_EOL;
        $body .= '     * 详情'.PHP_EOL;
        $body .= '     * @sdk '.strtolower($this->className).'Detail'.PHP_EOL;
        $body .= '     * @Route("/delete")'.PHP_EOL;
        $body .= '     * @input \App\Structs\Requests\\'.strtolower($this->className).'\DetailStruct'.PHP_EOL;
        $body .= '     * @output \App\Structs\Results\\'.strtolower($this->className).'\Row'.PHP_EOL;
        $body .= '     */'.PHP_EOL;
        $body .= '    public function detailAction()'.PHP_EOL;
        $body .= '    {'.PHP_EOL;
        $body .= '        $output = DetailLogic::factory($this->request->getJsonRawBody());'.PHP_EOL;
        $body .= '        return $this->serviceServer->withStruct($output);'.PHP_EOL;
        $body .= '    }'.PHP_EOL;
        $body .= PHP_EOL;
        return $body;
    }

    private function getBodyListing()
    {
        $body = '    /**'.PHP_EOL;
        $body .= '     * 全部列表'.PHP_EOL;
        $body .= '     * @sdk '.strtolower($this->className).'Listing'.PHP_EOL;
        $body .= '     * @Route("/listing")'.PHP_EOL;
        $body .= '     * @input \App\Structs\Requests\\'.strtolower($this->className).'\ListingStruct'.PHP_EOL;
        $body .= '     * @output \App\Structs\Results\\'.strtolower($this->className).'\Rows'.PHP_EOL;
        $body .= '     */'.PHP_EOL;
        $body .= '    public function listingAction()'.PHP_EOL;
        $body .= '    {'.PHP_EOL;
        $body .= '        $output = ListingLogic::factory($this->request->getJsonRawBody());'.PHP_EOL;
        $body .= '        return $this->serviceServer->withStruct($output);'.PHP_EOL;
        $body .= '    }'.PHP_EOL;
        $body .= PHP_EOL;
        return $body;
    }

    private function getBodyPaging()
    {
        $body = '    /**'.PHP_EOL;
        $body .= '     * 分页列表'.PHP_EOL;
        $body .= '     * @sdk '.strtolower($this->className).'Paging'.PHP_EOL;
        $body .= '     * @Route("/paging")'.PHP_EOL;
        $body .= '     * @input \App\Structs\Requests\\'.strtolower($this->className).'\PagingStruct'.PHP_EOL;
        $body .= '     * @output \App\Structs\Results\\'.strtolower($this->className).'\Rows'.PHP_EOL;
        $body .= '     */'.PHP_EOL;
        $body .= '    public function pagingAction()'.PHP_EOL;
        $body .= '    {'.PHP_EOL;
        $body .= '        $output = PagingLogic::factory($this->request->getJsonRawBody());'.PHP_EOL;
        $body .= '        return $this->serviceServer->withStruct($output);'.PHP_EOL;
        $body .= '    }'.PHP_EOL;
        $body .= PHP_EOL;
        return $body;
    }

    private function getBottom()
    {
        return '}'.PHP_EOL;
    }
}