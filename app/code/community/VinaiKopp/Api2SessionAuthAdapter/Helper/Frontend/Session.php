<?php


class VinaiKopp_Api2SessionAuthAdapter_Helper_Frontend_Session extends Mage_Core_Helper_Abstract
{
    /**
     * @var Mage_Core_Model_Session
     */
    protected $_coreSession;

    /**
     * @var Mage_Core_Model_Cookie
     */
    protected $_cookie;

    /**
     * @var Mage_Core_Model_App
     */
    protected $_app;

    /**
     * @return Mage_Core_Model_Session
     */
    public function getCoreSession()
    {
        if (!$this->_coreSession) {
            // @codeCoverageIgnoreStart
            $this->_coreSession = Mage::getSingleton(
                'core/session',
                array('name' => Mage_Core_Controller_Front_Action::SESSION_NAMESPACE)
            );
        }
        // @codeCoverageIgnoreEnd
        return $this->_coreSession;
    }

    /**
     * @param Mage_Core_Model_Session $session
     */
    public function setCoreSession(Mage_Core_Model_Session $session)
    {
        $this->_coreSession = $session;
    }

    /**
     * @return Mage_Core_Model_Cookie
     */
    public function getCookie()
    {
        if (is_null($this->_cookie)) {
            // @codeCoverageIgnoreStart
            $this->_cookie = Mage::getSingleton('core/cookie');
        }
        // @codeCoverageIgnoreEnd
        return $this->_cookie;
    }

    /**
     * @param Mage_Core_Model_Cookie $cookie
     */
    public function setCookie(Mage_Core_Model_Cookie $cookie)
    {
        $this->_cookie = $cookie;
    }

    /**
     * @return Mage_Core_Model_App
     */
    public function getApp()
    {
        if (is_null($this->_app)) {
            // @codeCoverageIgnoreStart
            $this->_app = Mage::app();
        }
        // @codeCoverageIgnoreEnd
        return $this->_app;
    }

    /**
     * @param Mage_Core_Model_App $app
     */
    public function setApp(Mage_Core_Model_App $app)
    {
        $this->_app = $app;
    }

    /**
     * Return true if the current request contains a frontend session cookie
     *
     * @return bool
     */
    public function hasFrontendSessionCookie()
    {
        return (bool)$this->getCookie()->get(Mage_Core_Controller_Front_Action::SESSION_NAMESPACE);
    }

    /**
     * Start the frontend session
     *
     * @return $this
     *
     * @throws Mage_Api2_Exception
     */
    public function startFrontendSession()
    {
        $this->getCoreSession()->start();

        return $this;
    }
}
