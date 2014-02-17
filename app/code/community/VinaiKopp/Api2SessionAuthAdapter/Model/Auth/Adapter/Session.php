<?php

/**
 * Class VinaiKopp_Api2SessionAuthAdapter_Model_Auth_Adapter_Session
 *
 * Provide a session based auth adapter for customer REST API requests.
 */
class VinaiKopp_Api2SessionAuthAdapter_Model_Auth_Adapter_Session
    extends Mage_Api2_Model_Auth_Adapter_Abstract
{
    const USER_TYPE_GUEST = 'guest';
    const USER_TYPE_CUSTOMER = 'customer';

    // See Mage_Core_Controller_Front_Action::SESSION_NAMESPACE
    const SESSION_NAMESPACE = 'frontend';

    /**
     * @var Mage_Core_Model_Cookie
     */
    protected $_cookie;

    /**
     * @var Mage_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * @var Mage_Core_Model_Session
     */
    protected $_coreSession;

    /**
     * @param Mage_Core_Model_Cookie $cookie
     * @param Mage_Customer_Model_Session $customerSession
     * @param Mage_Core_Model_Session $coreSession
     */
    public function __construct($cookie = null, $customerSession = null, $coreSession = null)
    {
        if ($cookie) {
            $this->_cookie = $cookie;
        }
        if ($customerSession) {
            $this->_customerSession = $customerSession;
        }
        if ($coreSession) {
            $this->_coreSession = $coreSession;
        }
    }

    /**
     * @return Mage_Core_Model_Cookie
     */
    public function getCookie()
    {
        if (!$this->_cookie) {
            // @codeCoverageIgnoreStart
            $this->_cookie = Mage::app()->getCookie();
        }
        // @codeCoverageIgnoreEnd
        return $this->_cookie;
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
     * @return Mage_Core_Model_Session
     */
    public function getCoreSession()
    {
        if (!$this->_coreSession) {
            // @codeCoverageIgnoreStart
            $this->_coreSession = Mage::getSingleton(
                'core/session',
                array('name' => self::SESSION_NAMESPACE)
            );
        }
        // @codeCoverageIgnoreEnd
        return $this->_coreSession;
    }

    /**
     * Return true if the current request contains a frontend session cookie
     *
     * @return bool
     */
    public function hasFrontendSession()
    {
        return (bool)$this->getCookie()->get(self::SESSION_NAMESPACE);
    }

    /**
     * @return string
     */
    public function getFrontendStoreCode()
    {
        $store = $this->getCookie()->get('store');
        if (! $store) {
            $store = Mage::app()->getDefaultStoreView()->getCode();
        }
        return $store;
    }

    /**
     * Start the frontend session
     */
    public function startFrontendSession()
    {
        $this->getCoreSession()->start();
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
        $userParamsObj = (object)array('type' => null, 'id' => null);
        if ($this->isApplicableToRequest($request)) {
            $userParamsObj->id = $this->getCustomerSession()->getCustomerId();
            $userParamsObj->type = self::USER_TYPE_CUSTOMER;
        }
        return $userParamsObj;
    }

    /**
     * Check if request contains authentication info for adapter
     *
     * @param Mage_Api2_Model_Request $request
     * @return boolean
     */
    public function isApplicableToRequest(Mage_Api2_Model_Request $request)
    {
        if ($this->hasFrontendSession()) {

            $store = $this->getFrontendStoreCode();
            Mage::app()->setCurrentStore($store);
            
            $this->startFrontendSession();
            
            return $this->getCustomerSession()->isLoggedIn();
        }
        return false;
    }
}