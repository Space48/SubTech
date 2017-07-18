<?php
/**
 * Sub2LibraryTest.php
 *
 * @Date        07/2017
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @author      @diazwatson
 */

namespace Space48\SubTech\Block;

use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Magento\TestFramework\ObjectManager;
use Space48\SubTech\Helper\Data;

class Sub2LibraryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var $block Sub2Library
     */
    public $block;

    public function setUp()
    {
        /** @var TemplateContext | \PHPUnit_Framework_MockObject_MockObject $mockTemplateContext */
        $mockTemplateContext = $this->getMockBuilder(TemplateContext::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var Data | \PHPUnit_Framework_MockObject_MockObject $mockSub2Helper */
        $objectManager = ObjectManager::getInstance()->create(Data::class);
        $mockSub2Helper = $objectManager;

        $this->block = new Sub2Library(
            $mockTemplateContext,
            $mockSub2Helper
        );
    }

    public function testItReturnsTrackingCodeWhenEnabled()
    {
        $this->assertEquals($this->getSampleOutPut(), $this->block->_toHtml());
    }

    /**
     * @return string
     */
    private function getSampleOutPut(): string
    {
        $expected = 'document.write(unescape("%3Cscript src=\'" 
        + document.location.protocol 
        + "//webservices.sub2tech.com/CodeBase/LIVE/Min/sub2.js?LICENSEKEY=4bef5b58-2b6a-4516-820b-6bbb330be272&trackPage=Y\' async=\'true\' type=\'text/javascript\'%3E%3C/script%3E"));
    var __s2tQ = __s2tQ || [];
';

        return $expected;
    }

}
