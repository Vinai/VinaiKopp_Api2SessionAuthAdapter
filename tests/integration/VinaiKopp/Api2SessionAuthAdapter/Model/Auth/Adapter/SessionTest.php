<?php


class VinaiKopp_Api2SessionAuthAdapter_Model_Auth_Adapter_SessionTest
    extends VinaiKopp_Framework_TestCase
{
    protected $_class = 'VinaiKopp_Api2SessionAuthAdapter_Model_Auth_Adapter_Session';

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
     * @param int $customerId
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getMockCustomerSession($customerId = null)
    {
        $mockSession = $this->getMockBuilder('Mage_Customer_Model_Session')
            ->disableOriginalConstructor()
            ->getMock();
        $mockSession->expects($this->any())
            ->method('getCustomerId')
            ->will($this->returnValue($customerId));
        $mockSession->expects($this->any())
            ->method('isLoggedIn')
            ->will($this->returnValue((bool) $customerId));
        return $mockSession;
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
    
    /**
     * @param PHPUnit_Framework_MockObject_MockObject $mockCookie
     * @param PHPUnit_Framework_MockObject_MockObject $mockCustomerSession
     * @param PHPUnit_Framework_MockObject_MockObject $mockCoreSession
     * @return VinaiKopp_Api2SessionAuthAdapter_Model_Auth_Adapter_Session
     */
    protected function _getInstance(
        $mockCookie = null, $mockCustomerSession = null, $mockCoreSession = null)
    {
        if (! $mockCookie) {
            $mockCookie = $this->_getMockCookie();
        }
        if (! $mockCustomerSession) {
            $mockCustomerSession = $this->_getMockCustomerSession();
        }
        if (! $mockCoreSession) {
            $mockCoreSession = $this->_getMockCoreSession();
        }
        return new $this->_class($mockCookie, $mockCustomerSession, $mockCoreSession);
    }
    
    public function testClassConfiguration()
    {
        $factoryName = 'vinaikopp_api2sessionauthadapter/auth_adapter_session';
        $this->assertEquals(
            $factoryName,
            (string) Mage::getConfig()->getNode('global/api2/auth_adapters/customer_session/model')
        );
        $class = Mage::getConfig()->getModelClassName($factoryName);
        $this->assertEquals($this->_class, $class);
        $this->assertTrue(class_exists($this->_class));
    }
    
    /**
     * @test
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
     */
    public function itHasAMethodHasFrontendSession()
    {
        $this->assertTrue(is_callable(array($this->_class, 'hasFrontendSession')));
    }

    /**
     * @test
     * @depends itHasAMethodHasFrontendSession
     */
    public function itReturnsTrueWhenAFrontendCookieIsPresent()
    {
        $mockCookie = $this->_getMockCookie('1234567890'); // dummy session ID

        $model = $this->_getInstance($mockCookie);
        
        $this->assertTrue($model->hasFrontendSession());
    }

    /**
     * @test
     * @depends itHasAMethodHasFrontendSession
     */
    public function itReturnsFalseWhenNoFrontendCookieIsPresent()
    {
        $mockCookie = $this->_getMockCookie(false);

        $model = new $this->_class($mockCookie);
        
        $result = $mockCookie->get('frontend');
        
        $this->assertFalse($model->hasFrontendSession());
    }

    /**
     * @test
     */
    public function itHasAMethodGetCustomerSession()
    {
        $this->assertTrue(is_callable(array($this->_class, 'getCustomerSession')));
    }

    /**
     * @test
     * @depends itHasAMethodGetCustomerSession
     */
    public function itReturnsACustomerSessionModel()
    {
        $model = $this->_getInstance();

        $this->assertInstanceOf('Mage_Customer_Model_Session', $model->getCustomerSession());
    }

    /**
     * @test
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
     */
    public function itHasAMethodGetFrontendStoreCode()
    {
        $this->assertTrue(is_callable(array($this->_class, 'getFrontendStoreCode')));
    }

    /**
     * @test
     * @depends itHasAMethodGetFrontendStoreCode
     */
    public function itReturnsTheStoreCookieValueIfSet()
    {
        $mockCookie = $this->_getMockCookie(false, 'test');
        
        $model = $this->_getInstance($mockCookie);
        $this->assertEquals('test', $model->getFrontendStoreCode());
    }

    /**
     * @test
     * @depends itHasAMethodGetFrontendStoreCode
     */
    public function itReturnsTheDefaultStoreIfNoStoreCookieIsSet()
    {
        $mockCookie = $this->_getMockCookie();

        $expected = Mage::app()->getDefaultStoreView()->getCode();

        $model = $this->_getInstance($mockCookie);
        $this->assertEquals($expected, $model->getFrontendStoreCode());
    }

    /**
     * @test
     */
    public function itHasAMethodGetUserParams()
    {
        $this->assertTrue(is_callable(array($this->_class, 'getUserParams')));
    }

    /**
     * @test
     * @depends itHasAMethodGetUserParams
     */
    public function itReturnsUserParamsCustomerForValidSession()
    {
        $mockCookie = $this->_getMockCookie('1234567890', 'default'); // dummy session ID + store
        $mockSession = $this->_getMockCustomerSession(1); // dummy customer ID
        $model = $this->_getInstance($mockCookie, $mockSession);
        $mockRequest = $this->getMock('Mage_Api2_Model_Request');
        
        $result = $model->getUserParams($mockRequest);
        
        $this->assertTrue(is_object($result));
        $this->assertEquals(1, $result->id);
        $this->assertEquals('customer', $result->type);
    }

    /**
     * @test
     * @depends itHasAMethodGetUserParams
     */
    public function itReturnsUserParamsNullIfNoFrontendCookieExists()
    {
        $mockCookie = $this->_getMockCookie(false, 'default'); // no session ID
        $model = $this->_getInstance($mockCookie);
        $mockRequest = $this->getMock('Mage_Api2_Model_Request');
        
        $result = $model->getUserParams($mockRequest);
        
        $this->assertTrue(is_object($result));
        $this->assertNull($result->id);
        $this->assertNull($result->type);
    }

    /**
     * @test
     * @depends itHasAMethodGetUserParams
     */
    public function itReturnsUserParamsNullIfNotLoggedInButFrontendCookieExists()
    {
        $mockCookie = $this->_getMockCookie('1234567890', 'default'); // dummy session ID
        $mockSession = $this->_getMockCustomerSession(null); // no customer ID
        $model = $this->_getInstance($mockCookie, $mockSession);
        $mockRequest = $this->getMock('Mage_Api2_Model_Request');
        
        $result = $model->getUserParams($mockRequest);
        
        $this->assertTrue(is_object($result));
        $this->assertNull($result->id);
        $this->assertNull($result->type);
    }
}