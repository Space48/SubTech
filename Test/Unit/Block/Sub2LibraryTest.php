<?php
/**
 * Sub2LibraryTest.php
 *
 * @Date        07/2017
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @author      @diazwatson
 */

namespace Space48\SubTech\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Space48\SubTech\Helper\Data;

class Sub2LibraryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var $block Sub2Library
     */
    public $block;

    public function setUp()
    {
        /** @var Context | \PHPUnit_Framework_MockObject_MockObject $mockContext */
        $mockContext = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var Data | \PHPUnit_Framework_MockObject_MockObject $mockSub2Helper */
        $mockSub2Helper = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->block = new Sub2Library(
            $mockContext,
            $mockSub2Helper
        );
    }

    public function testSub2LibraryBlockIsInstanceOfTemplate()
    {
        $this->assertInstanceOf(Template::class, $this->block);
    }

    public function testToHtmlReturnsTheRightType()
    {
        $this->assertInternalType('string', $this->block->_toHtml());
    }

}
