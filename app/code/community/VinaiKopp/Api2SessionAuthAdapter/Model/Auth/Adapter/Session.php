<?php

/**
 * Class VinaiKopp_Api2SessionAuthAdapter_Model_Auth_Adapter_Session
 *
 * Provide a session based auth adapter for customer REST API requests.
 */
class VinaiKopp_Api2SessionAuthAdapter_Model_Auth_Adapter_Session
    extends Mage_Api2_Model_Auth_Adapter_Abstract
{
    const USER_TYPE_CUSTOMER = 'customer';

    /**
     * @var VinaiKopp_Api2SessionAuthAdapter_Helper_Frontend_Session
     */
    protected $_helper;

    /**
     * @var Mage_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * @param VinaiKopp_Api2SessionAuthAdapter_Helper_Frontend_Session $helper
     * @param Mage_Customer_Model_Session $customerSession
     */
    public function __construct($helper = null, $customerSession = null)
    {
        if ($helper) {
            $this->_helper = $helper;
        }
        if ($customerSession) {
            $this->_customerSession = $customerSession;
        }
    }

    public function getHelper()
    {
        if (!$this->_helper) {
            // @codeCoverageIgnoreStart
            $this->_helper = Mage::helper('vinaikopp_api2sessionauthadapter/frontend_session');
        }
        // @codeCoverageIgnoreEnd
        return $this->_helper;
    }

    /**
     * @return Mage_Customer_Model_Session
     */
    public function getCustomerSession()
    {
        if (!$this->_customerSession) {
            // @codeCoverageIgnoreStart
            $this->_customerSession = Mage::getSingleton('customer/session');
        }
        // @codeCoverageIgnoreEnd
        return $this->_customerSession;
    }

    /**
     * Process request and figure out an API user type and its identifier
     *
     * Returns stdClass object with two properties: type and id
     *
     * @param Mage_Api2_Model_Request $request
     * @return stdClass
     */
    public function getUserParams(Mage_Api2_Model_Request $request)
    {
        return (object)array(
            'id' => $this->getCustomerSession()->getCustomerId(),
            'type' => self::USER_TYPE_CUSTOMER
        );
    }

    /**
     * Check if request contains authentication info for adapter
     *
     * @param Mage_Api2_Model_Request $request
     * @return boolean
     */
    public function isApplicableToRequest(Mage_Api2_Model_Request $request)
    {
        $helper = $this->getHelper();

        // Ensure frontend sessions are initialized using the proper cookie name
        $helper->startFrontendSession();

        // We are only applicable if the customer is logged in already
        return $this->getCustomerSession()->isLoggedIn();
    }
}
