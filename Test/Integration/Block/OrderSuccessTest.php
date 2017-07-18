<?php
/**
 * OrderSuccessTest.php
 *
 * @Date        07/2017
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @author      @diazwatson
 */

namespace Space48\SubTech\Block;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Sales\Model\Order;
use Magento\TestFramework\ObjectManager;

class OrderSuccessTest extends \PHPUnit_Framework_TestCase
{

    /** @var Order $order */
    public $order;

    /** @var  OrderSuccess */
    protected $block;

    public function setUp()
    {
        $this->block = ObjectManager::getInstance()->create(OrderSuccess::class);

    }

    public function testOrderSuccessBlockIsInstanceOfTemplate()
    {
        /**
         * @return Template|\PHPUnit_Framework_MockObject_MockObject
         */
        $this->assertInstanceOf(Template::class, $this->block);
    }

    public function testToHtmlReturnsTheRightType()
    {
        $this->assertInternalType('string', $this->block->_toHtml());
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDataFixture Magento/Sales/_files/order_with_shipping_and_invoice.php
     */
    public function testToHtmlReturnsRightOrderDataWhenOrderIdIsProvided()
    {
        /** @var Order $order */
        $order = ObjectManager::getInstance()->create(Order::class);
        $order->loadByIncrementId('100000001');

        /** @var Registry $registry */
        $registry = ObjectManager::getInstance()->get(Registry::class);

        $registry->register('orderIds', [$order->getData('entity_id')]);

        $expected = $this->getSampleOutPutData();
        $this->assertEquals($expected, $this->block->_toHtml());
    }


    public function testToHtmlReturnsEmptyStringWhenOrderIdIsNotProvided()
    {
        $this->assertEquals('', $this->block->_toHtml());
    }

    /**
     * @return string
     */
    public function getSampleOutPutData()
    {
        return '__s2tQ.push([\'addOrder\' ,{"OrderID":"100000001","Total":"100.0000","Shipping":"20.0000","City":"Los Angeles","County":"CA","Country":"US"}]);

__s2tQ.push([\'storeData\' ,{"Address1":"street","Address2":"Los Angeles","Address3":"CA","Address4":"US","Postcode":"11111","Landline":"11111111","Email":"customer@null.com"}]);

__s2tQ.push([\'addItem\' ,{"OrderID":"100000001","Product_ID":"1","Category":"Default Category","Unit_Price":"10.0000","Quantity":"2.0000"}]);
';
    }
}
