<?php


class VinaiKopp_Api2SessionAuthAdapter_Helper_DataTest extends VinaiKopp_Framework_TestCase
{

    protected $class = 'VinaiKopp_Api2SessionAuthAdapter_Helper_Data';

    /**
     * @return VinaiKopp_Api2SessionAuthAdapter_Helper_Data
     */
    public function getInstance()
    {
        return new $this->class;
    }

    public function testClassExists()
    {
        $factoryName = 'vinaikopp_api2sessionauthadapter';
        $class = Mage::getConfig()->getHelperClassName($factoryName);
        $this->assertEquals($this->class, $class);
        $this->assertTrue(class_exists($class));
        $this->assertInstanceOf('Mage_Core_Helper_Abstract', new $class);
    }
} 