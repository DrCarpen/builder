<?php
/**
 * @author liyang <liyang@uniondrug.cn>
 * @date   2019-05-06
 */
namespace Uniondrug\Builder\Parsers;

use Uniondrug\Builder\Parsers\Abstracts\Construct;

class ConstructService extends Construct
{
    protected $fileType = 'service';

    public function __construct($dbConfig, $authorConfig)
    {
        parent::__construct($dbConfig, $authorConfig);
    }

    /**
     * 核心方法
     */
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
        $head .= 'namespace App\Services;'.PHP_EOL.PHP_EOL;
        $head .= 'use App\Services\Abstracts\Service;'.PHP_EOL;
        $head .= 'use App\Structs\Requests\\'.$this->className.'\CreateStruct;'.PHP_EOL;
        $head .= 'use App\Structs\Requests\\'.$this->className.'\DeleteStruct;'.PHP_EOL;
        $head .= 'use App\Structs\Requests\\'.$this->className.'\UpdateStruct;'.PHP_EOL;
        $head .= 'use App\Structs\Requests\\'.$this->className.'\DetailStruct;'.PHP_EOL;
        $head .= 'use App\Structs\Requests\\'.$this->className.'\ListingStruct;'.PHP_EOL;
        $head .= 'use App\Structs\Requests\\'.$this->className.'\PagingStruct;'.PHP_EOL;
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
        $property .= ' * Class '.$this->className.'Service'.PHP_EOL;
        $property .= ' * @package App\Services'.PHP_EOL;
        $property .= ' */'.PHP_EOL;
        $property .= 'class '.$this->className.'Service extends Service'.PHP_EOL;
        return $property;
    }

    private function getBodyCreate()
    {
        $body = '{'.PHP_EOL;
        $body .= '    public function create(CreateStruct $struct)'.PHP_EOL;
        $body .= '    {'.PHP_EOL;
        $body .= '    }'.PHP_EOL;
        $body .= PHP_EOL;
        return $body;
    }

    private function getBodyUpdate()
    {
        $body = '    public function update(UpdateStruct $struct)'.PHP_EOL;
        $body .= '    {'.PHP_EOL;
        $body .= '    }'.PHP_EOL;
        $body .= PHP_EOL;
        return $body;
    }

    private function getBodyDelete()
    {
        $body = '    public function delete(DeleteStruct $struct)'.PHP_EOL;
        $body .= '    {'.PHP_EOL;
        $body .= '    }'.PHP_EOL;
        $body .= PHP_EOL;
        return $body;
    }

    private function getBodyDetail()
    {
        $body = '    public function detail(DetailStruct $struct)'.PHP_EOL;
        $body .= '    {'.PHP_EOL;
        $body .= '    }'.PHP_EOL;
        $body .= PHP_EOL;
        return $body;
    }

    private function getBodyListing()
    {
        $body = '    public function listing(ListingStruct $struct)'.PHP_EOL;
        $body .= '    {'.PHP_EOL;
        $body .= '    }'.PHP_EOL;
        $body .= PHP_EOL;
        return $body;
    }

    private function getBodyPaging()
    {
        $body = '    public function paging(PagingStruct $struct)'.PHP_EOL;
        $body .= '    {'.PHP_EOL;
        $body .= '    }'.PHP_EOL;
        $body .= PHP_EOL;
        return $body;
    }

    private function getBottom()
    {
        return '}'.PHP_EOL;
    }
}