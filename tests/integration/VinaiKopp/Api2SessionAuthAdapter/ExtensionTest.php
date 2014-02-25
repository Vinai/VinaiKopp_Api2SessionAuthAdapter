<?php


class VinaiKopp_Api2SessionAuthAdapter_ExtensionTest extends VinaiKopp_Framework_TestCase
{
    public function testExtensionInstalled()
    {
        $config = Mage::getConfig()->getNode('modules/VinaiKopp_Api2SessionAuthAdapter/active');
        $this->assertEquals('true', "$config");
    }

    public function testRequestWithoutSession()
    {
        $this->resetMagento();
        Mage::app()->getRequest()->setParam('type', 'rest');
        $_SERVER['HTTP_ACCEPT'] = 'application/json';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $response = $this->getMockBuilder('Mage_Api2_Model_Response')
            ->disableOriginalConstructor()
            ->getMock();
        Mage::register('_singleton/api2/response', $response);

        $response->expects($this->once())
            ->method('setHttpResponseCode')
            ->with(403)
            ->will($this->returnSelf());
        $response->expects($this->once())
            ->method('setBody')
            ->withAnyParameters()
            ->will($this->returnSelf());
        $response->expects($this->once())
            ->method('sendResponse')
            ->withAnyParameters()
            ->will($this->returnSelf());
        $response->expects($this->once())
            ->method('setException');
        $response->expects($this->once())
            ->method('getException')
            ->with()
            ->will($this->returnValue(array()));
        
        Mage::getSingleton('api2/request')->setPathInfo('/api/rest/customers/1/addresses');

        $server = Mage::getSingleton('api2/server');
        $server->run();
    }

    public function testRequestWithSession()
    {
        $this->resetMagento();
        $_SERVER['HTTP_ACCEPT'] = 'application/json';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $mockCookie = $this->getMockBuilder('Mage_Core_Model_Cookie')
            ->disableOriginalConstructor()
            ->getMock();
        $mockCookie->expects($this->atLeastOnce())
            ->method('get')
            ->will($this->returnValueMap(array(
                array('frontend', '1234567'), // dummy session ID
                array('store', 'default')
            )));
        Mage::unregister('_singleton/core/cookie');
        Mage::register('_singleton/core/cookie', $mockCookie);
        
        $mockCustomerSession = $this->getMockBuilder('Mage_Customer_Model_Session')
            ->disableOriginalConstructor()
            ->getMock();
        $mockCustomerSession->expects($this->once())
            ->method('getCustomerId')
            ->will($this->returnValue(1)); // dummy customer ID
        $mockCustomerSession->expects($this->atLeastOnce())
            ->method('isLoggedIn')
            ->will($this->returnValue(true));
        Mage::register('_singleton/customer/session', $mockCustomerSession);

        $mockCoreSession = $this->getMockBuilder('Mage_Core_Model_Session')
            ->disableOriginalConstructor()
            ->getMock();
        Mage::register('_singleton/core/session', $mockCoreSession);

        Mage::app()->getRequest()->setParam('type', 'rest');
        
        $response = $this->getMockBuilder('Mage_Api2_Model_Response')
            ->disableOriginalConstructor()
            ->getMock();
        Mage::register('_singleton/api2/response', $response);

        $response->expects($this->once())
            ->method('setBody')
            ->withAnyParameters()
            ->will($this->returnSelf());
        $response->expects($this->once())
            ->method('sendResponse')
            ->withAnyParameters()
            ->will($this->returnSelf());
        
        $mockAcl = $this->getMock('Mage_Api2_Model_Acl_Global');
        $mockAcl->expects($this->once())
            ->method('isAllowed')
            ->withAnyParameters()
            ->will($this->returnValue(true));
        Mage::getConfig()->setModelMock('api2/acl_global', $mockAcl);
        
        Mage::getSingleton('api2/request')->setPathInfo('/api/rest/customers/1/addresses');

        $server = Mage::getSingleton('api2/server');
        $server->run();
    }
}