<?php


class VinaiKopp_Api2SessionAuthAdapter_Helper_Frontend_SessionTest extends VinaiKopp_Framework_TestCase
{

    protected $_class = 'VinaiKopp_Api2SessionAuthAdapter_Helper_Frontend_Session';

    /**
     * @param PHPUnit_Framework_MockObject_MockObject $mockCookie
     * @param PHPUnit_Framework_MockObject_MockObject $mockSession
     * @return VinaiKopp_Api2SessionAuthAdapter_Helper_Frontend_Session
     */
    protected function _getInstance($mockCookie = null, $mockSession = null)
    {
        /** @var VinaiKopp_Api2SessionAuthAdapter_Helper_Frontend_Session $helper */
        $helper = new $this->_class;
        if (! $mockCookie) {
            $mockCookie = $this->_getMockCookie();
        }
        if (! $mockSession) {
            $mockSession = $this->_getMockCoreSession();
        }
        $helper->setCookie($mockCookie);
        $helper->setCoreSession($mockSession);
        
        return $helper;
    }

    /**
     * @param mixed $frontendCookie
     * @param string $storeCode
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getMockCookie($frontendCookie = false, $storeCode = null)
    {
        $mockCookie = $this->getMockBuilder('Mage_Core_Model_Cookie')
            ->disableOriginalConstructor()
            ->getMock();
        $mockCookie->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap(array(
                array('frontend', $frontendCookie),
                array('store', $storeCode)
            )));
        return $mockCookie;
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getMockCoreSession()
    {
        $mockSession = $this->getMockBuilder('Mage_Core_Model_Session')
            ->disableOriginalConstructor()
            ->getMock();
        return $mockSession;
    }

    public function testClassExists()
    {
        $factoryName = 'vinaikopp_api2sessionauthadapter/frontend_session';
        $class = Mage::getConfig()->getHelperClassName($factoryName);
        $this->assertEquals($this->_class, $class);
        $this->assertTrue(class_exists($class));
        $this->assertInstanceOf('Mage_Core_Helper_Abstract', new $class);
    }

    /**
     * @test
     * @depends testClassExists
     */
    public function itHasAMethodGetCoreSession()
    {
        $this->assertTrue(is_callable(array($this->_class, 'getCoreSession')));
    }

    /**
     * @test
     * @depends itHasAMethodGetCoreSession
     */
    public function itReturnsACoreSessionModel()
    {
        $model = $this->_getInstance();

        $this->assertInstanceOf('Mage_Core_Model_Session', $model->getCoreSession());
    }

    /**
     * @test
     * @depends testClassExists
     */
    public function itHasAMethodSetCoreSession()
    {
        $this->assertTrue(is_callable(array($this->_class, 'setCoreSession')));
    }

    /**
     * @test
     * @depends testClassExists
     */
    public function itHasAMethodGetCookie()
    {
        $this->assertTrue(is_callable(array($this->_class, 'getCookie')));
    }

    /**
     * @test
     * @depends itHasAMethodGetCookie
     */
    public function itReturnsACoreCookieInstance()
    {
        $model = $this->_getInstance();

        $this->assertInstanceOf('Mage_Core_Model_Cookie', $model->getCookie());
    }

    /**
     * @test
     * @depends testClassExists
     */
    public function itHasAMethodSetCookie()
    {
        $this->assertTrue(is_callable(array($this->_class, 'setCookie')));
    }

    /**
     * @test
     * @depends testClassExists
     */
    public function itHasAMethodSetApp()
    {
        $this->assertTrue(is_callable(array($this->_class, 'setApp')));
    }

    /**
     * @test
     * @depends testClassExists
     */
    public function itHasAMethodGetApp()
    {
        $this->assertTrue(is_callable(array($this->_class, 'getApp')));
    }
    /**
     * @test
     */
    public function itHasAMethodHasFrontendSessionCookie()
    {
        $this->assertTrue(is_callable(array($this->_class, 'hasFrontendSessionCookie')));
    }

    /**
     * @test
     * @depends itHasAMethodHasFrontendSessionCookie
     */
    public function itReturnsTrueWhenAFrontendCookieIsPresent()
    {
        $mockCookie = $this->_getMockCookie('1234567890'); // dummy session ID

        $model = $this->_getInstance($mockCookie);

        $this->assertTrue($model->hasFrontendSessionCookie());
    }

    /**
     * @test
     * @depends itHasAMethodHasFrontendSessionCookie
     */
    public function itReturnsFalseWhenNoFrontendCookieIsPresent()
    {
        $mockCookie = $this->_getMockCookie(false);

        /** @var VinaiKopp_Api2SessionAuthAdapter_Helper_Frontend_Session $model */
        $model = new $this->_class($mockCookie);

        $this->assertFalse($model->hasFrontendSessionCookie());
    }

    /**
     * @test
     * @depends testClassExists
     */
    public function itHasAMethodStartFrontendSession()
    {
        $this->assertTrue(is_callable(array($this->_class, 'startFrontendSession')));
    }

    /**
     * @test
     * @depends itHasAMethodStartFrontendSession
     */
    public function itCallsStartOnTheCoreCookieModel()
    {
        $mockSession = $this->_getMockCoreSession();
        $mockSession->expects($this->once())
            ->method('start');
        $model = $this->_getInstance(null, $mockSession);
        $model->startFrontendSession();
    }
}
