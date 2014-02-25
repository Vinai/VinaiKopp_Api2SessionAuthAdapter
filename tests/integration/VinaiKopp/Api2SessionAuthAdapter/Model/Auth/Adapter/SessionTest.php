<?php


class VinaiKopp_Api2SessionAuthAdapter_Model_Auth_Adapter_SessionTest
    extends VinaiKopp_Framework_TestCase
{
    protected $_class = 'VinaiKopp_Api2SessionAuthAdapter_Model_Auth_Adapter_Session';
    
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
     * @param bool $hasFrontendSession
     * @param string $frontendStoreCode
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getMockHelper($hasFrontendSession = false, $frontendStoreCode = 'default')
    {
        $mockHelper = $this->getMock('VinaiKopp_Api2SessionAuthAdapter_Helper_Frontend_Session');
        $mockHelper->expects($this->any())
            ->method('hasFrontendSession')
            ->will($this->returnValue($hasFrontendSession));
        $mockHelper->expects($this->any())
            ->method('getFrontendStoreCode')
            ->will($this->returnValue($frontendStoreCode));
        $mockHelper->expects($this->any())
            ->method('startFrontendSession')
            ->will($this->returnSelf());
        $mockHelper->expects($this->any())
            ->method('initFrontendStore')
            ->will($this->returnSelf());
        
        return $mockHelper;
    }
    
    /**
     * @param PHPUnit_Framework_MockObject_MockObject $mockHelper
     * @param PHPUnit_Framework_MockObject_MockObject $mockCustomerSession
     * @return VinaiKopp_Api2SessionAuthAdapter_Model_Auth_Adapter_Session
     */
    protected function _getInstance($mockHelper = null, $mockCustomerSession = null)
    {
        if (! $mockHelper) {
            $mockHelper = $this->_getMockHelper();
        }
        if (! $mockCustomerSession) {
            $mockCustomerSession = $this->_getMockCustomerSession();
        }
        return new $this->_class($mockHelper, $mockCustomerSession);
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
        $mockHelper = $this->_getMockHelper(true, 'default');
        $mockSession = $this->_getMockCustomerSession(1); // dummy customer ID
        $model = $this->_getInstance($mockHelper, $mockSession);
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
    public function itReturnsUserParamsNullIfNoFrontendSessionExists()
    {
        $model = $this->_getInstance();
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
    public function itReturnsUserParamsNullIfNotLoggedInButFrontendSessionExists()
    {
        $mockHelper = $this->_getMockHelper(true);
        $mockSession = $this->_getMockCustomerSession(null); // no customer ID
        $model = $this->_getInstance($mockHelper, $mockSession);
        $mockRequest = $this->getMock('Mage_Api2_Model_Request');
        
        $result = $model->getUserParams($mockRequest);
        
        $this->assertTrue(is_object($result));
        $this->assertNull($result->id);
        $this->assertNull($result->type);
    }

    /**
     * @test
     */
    public function itHasAMethodIsApplicableToRequest()
    {
        $this->assertTrue(is_callable(array($this->_class, 'isApplicableToRequest')));
    }

    /**
     * @test
     * @depends itHasAMethodIsApplicableToRequest
     */
    public function itReturnsFalseIfNoFrontendSessionExists()
    {
        $mockRequest = $this->getMock('Mage_Api2_Model_Request');
        
        $model = $this->_getInstance();
        $result = $model->isApplicableToRequest($mockRequest);
        $this->assertFalse($result);
    }

    /**
     * @test
     * @depends itHasAMethodIsApplicableToRequest
     */
    public function itReturnsTrueIfFrontendSessionExistsAndCustomerIsLoggedIn()
    {
        $mockHelper = $this->_getMockHelper(true);
        $mockSession = $this->_getMockCustomerSession(1); // dummy customer ID
        $mockRequest = $this->getMock('Mage_Api2_Model_Request');
        $model = $this->_getInstance($mockHelper, $mockSession);
        
        $result = $model->isApplicableToRequest($mockRequest);
        $this->assertTrue($result);
    }

    /**
     * @test
     * @depends itHasAMethodIsApplicableToRequest
     */
    public function itReturnsFalseIfFrontendSessionExistsButCustomerIsNotLoggedIn()
    {
        $mockHelper = $this->_getMockHelper(true);
        $mockRequest = $this->getMock('Mage_Api2_Model_Request');
        $model = $this->_getInstance($mockHelper);
        
        $result = $model->isApplicableToRequest($mockRequest);
        $this->assertFalse($result);
    }
}