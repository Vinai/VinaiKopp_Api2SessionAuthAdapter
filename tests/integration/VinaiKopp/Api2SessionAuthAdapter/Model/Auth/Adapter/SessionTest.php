<?php


class VinaiKopp_Api2SessionAuthAdapter_Model_Auth_Adapter_SessionTest
    extends VinaiKopp_Framework_TestCase
{
    protected $_class = 'VinaiKopp_Api2SessionAuthAdapter_Model_Auth_Adapter_Session';

    /**
     * @param mixed $frontendCookie
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getMockCookie($frontendCookie = false)
    {
        $mockCookie = $this->getMockBuilder('Mage_Core_Model_Cookie')
            ->disableOriginalConstructor()
            ->getMock();
        $mockCookie->expects($this->any())
            ->method('get')
            ->with('frontend')
            ->will($this->returnValue($frontendCookie));
        return $mockCookie;
    }
    
    /**
     * @param int $customerId
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getMockSession($customerId = null)
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
     * @param PHPUnit_Framework_MockObject_MockObject $mockCookie
     * @param PHPUnit_Framework_MockObject_MockObject $mockSession
     * @return VinaiKopp_Api2SessionAuthAdapter_Model_Auth_Adapter_Session
     */
    protected function _getInstance($mockCookie = null, $mockSession = null)
    {
        if (! $mockCookie) {
            $mockCookie = $this->_getMockCookie();
        }
        if (! $mockSession) {
            $mockSession = $this->_getMockSession();
        }
        return new $this->_class($mockCookie, $mockSession);
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
        
        $this->assertFalse($model->hasFrontendSession());
    }

    /**
     * @test
     */
    public function itHasAMethodGetSession()
    {
        $this->assertTrue(is_callable(array($this->_class, 'getSession')));
    }

    /**
     * @test
     * @depends itHasAMethodGetSession
     */
    public function itReturnsACustomerSessionModel()
    {
        $model = $this->_getInstance();

        $this->assertInstanceOf('Mage_Customer_Model_Session', $model->getSession());
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
        $mockCookie = $this->_getMockCookie('1234567890'); // dummy session ID
        $mockSession = $this->_getMockSession(1); // dummy customer ID
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
        $mockCookie = $this->_getMockCookie(false); // no session ID
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
        $mockCookie = $this->_getMockCookie('1234567890'); // dummy session ID
        $mockSession = $this->_getMockSession(null); // no customer ID
        $model = $this->_getInstance($mockCookie, $mockSession);
        $mockRequest = $this->getMock('Mage_Api2_Model_Request');
        
        $result = $model->getUserParams($mockRequest);
        
        $this->assertTrue(is_object($result));
        $this->assertNull($result->id);
        $this->assertNull($result->type);
    }
}