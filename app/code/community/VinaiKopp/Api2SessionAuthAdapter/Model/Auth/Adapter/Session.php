<?php

/**
 * Class VinaiKopp_Api2SessionAuthAdapter_Model_Auth_Adapter_Session
 * 
 * Provide a session based auth adapter for customer REST API requests.
 */
class VinaiKopp_Api2SessionAuthAdapter_Model_Auth_Adapter_Session
    extends Mage_Api2_Model_Auth_Adapter_Abstract
{
    const USER_TYPE_GUEST    = 'guest';
    const USER_TYPE_CUSTOMER = 'customer';
    
    /**
     * @var Mage_Core_Model_Cookie
     */
    protected $_cookie;

    /**
     * @var Mage_Customer_Model_Session
     */
    protected $_session;

    /**
     * @param Mage_Core_Model_Cookie $cookie
     * @param Mage_Customer_Model_Session $session
     */
    public function __construct(Mage_Core_Model_Cookie $cookie = null, Mage_Customer_Model_Session $session = null)
    {
        $this->_cookie = $cookie;
        $this->_session = $session;
    }

    /**
     * @return Mage_Core_Model_Cookie
     */
    public function getCookie()
    {
        if (! $this->_cookie) {
            // @codeCoverageIgnoreStart
            $this->_cookie = Mage::app()->getCookie();
        }
        // @codeCoverageIgnoreEnd
        return $this->_cookie;
    }

    /**
     * @return Mage_Customer_Model_Session
     */
    public function getSession()
    {
        if (! $this->_session) {
            // @codeCoverageIgnoreStart
            $this->_session = Mage::getSingleton('customer/session');
        }
        // @codeCoverageIgnoreEnd
        return $this->_session;
    }

    /**
     * Return true if the current request contains a frontend session cookie
     * 
     * @return bool
     */
    public function hasFrontendSession()
    {
        return (bool) $this->getCookie()->get(Mage_Core_Controller_Front_Action::SESSION_NAMESPACE);
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
        $userParamsObj = (object) array('type' => null, 'id' => null);
        if ($this->isApplicableToRequest($request)) {
            $userParamsObj->id = $this->getSession()->getCustomerId();
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
        return $this->hasFrontendSession() && $this->getSession()->isLoggedIn();
    }

} 