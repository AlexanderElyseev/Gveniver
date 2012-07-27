<?php
/**
 * File contains session storage class for native PHP session functionality.
 *
 * @category  Gveniver
 * @package   Session
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

namespace Gveniver\Session\Storage;

/**
 * Session storage class for native PHP session functionality.
 *
 * @category  Gveniver
 * @package   Session
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class NativeSessionStorage extends BaseSessionStorage
{
    /**
     * Template method for initialization of provider. Is called from constructor.
     * Overriden for configuring of PHP native session system.
     *
     * @return void
     */
    public function init()
    {
        $aConfig = $this->getConfig();
        if ($aConfig) {
            if (isset($aConfig['CookieHttpOnly']))
                $this->getApplication()->trace->addLine('[%s] Using HttpOnly cookies.', __CLASS__);
                ini_set('session.cookie_httponly', \Gveniver\toBoolean($aConfig['CookieHttpOnly']));

            if (isset($aConfig['CookieDomain']))
                $this->getApplication()->trace->addLine('[%s] Using "%s" as cookie domain.', __CLASS__, $aConfig['CookieDomain']);
                ini_set('session.cookie_domain', $aConfig['CookieDomain']);
        }

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Starts the session.
     *
     * @return void
     */
    public function start()
    {
        $sId = $this->getId();
        if ($sId)
            session_id($sId);
        else
            $this->setId(session_id());

        session_start();

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Migrates the current session to a new session id while maintaining all session attributes.
     *
     * @return void
     */
    public function migrate()
    {
        session_regenerate_id();
        $this->setId(session_id());

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Invalidates the current session. Clears all session attributes.
     *
     * @return void
     */
    public function invalidate()
    {
        session_destroy();

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Gets session data from the persistence.
     *
     * @return array
     */
    public function get()
    {
        return $_SESSION;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Sets session data at the persistence.
     *
     * @param array $aSession Data for saving.
     *
     * @return void
     */
    public function set(array $aSession)
    {
        $_SESSION = $aSession;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Cleans all session data at the persistence.
     *
     * @return void
     */
    public function clean()
    {
        $_SESSION = array();

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------