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
     * Constructs new instance of {@see \Gveniver\Session\Storage\NativeSessionStorage}.
     *
     * @param \Gveniver\Kernel\Application $cApp    Current application.
     * @param array                        $aConfig Configuration parameters.
     */
    public function __construct(\Gveniver\Kernel\Application $cApp, array $aConfig = array())
    {
        parent::__construct($cApp);

        if ($aConfig) {
            if (isset($aConfig['CookieHttpOnly']))
                $cApp->trace->addLine('[%s] Using HttpOnly cookies.', __CLASS__);
                ini_set('session.cookie_httponly', \Gveniver\Kernel\Application::toBoolean($aConfig['CookieHttpOnly']));

            if (isset($aConfig['CookieDomain']))
                $cApp->trace->addLine('[%s] Using "%s" as cookie domain.', __CLASS__, $aConfig['CookieDomain']);
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
     * Invalidates the current session. Clears all session attributes. Migrates to new session.
     *
     * @return void
     */
    public function invalidate()
    {
        session_destroy();
        session_start();

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Returns value of attribute with specified name.
     * If attribute is not exist, returns default value.
     *
     * @param string $sName    The name of attribute for saving.
     * @param mixed  $mDefault The default value if not found.
     *
     * @return mixed Value of attribute.
     */
    public function get($sName, $mDefault = null)
    {
        $aArr = &$_SESSION;
        $aPathItems = $this->getPath($sName);
        $nCountPathItems = count($aPathItems);
        for ($i = 0; $i < $nCountPathItems - 1; $i++) {
            if (!is_array($aArr) || !isset($aArr[$aPathItems[$i]]))
                return $mDefault;
            else
                $aArr = &$aArr[$aPathItems[$i]];
        }

        return isset($aArr[$aPathItems[$nCountPathItems - 1]])
            ? $aArr[$aPathItems[$nCountPathItems - 1]]
            : $mDefault;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Returns all data in session.
     *
     * @return array
     */
    public function getAll()
    {
        return $_SESSION;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Sets value of attribute with specified name.
     *
     * Overrides old values of nested data. Example: if we have data in a/b=5 and we write a/b/c=6, then
     * value in a/b=5 will be cleaned.
     *
     * @param string $sName  The name of attribute for saving.
     * @param mixed  $mValue The value of attribute for saving.
     *
     * @return void
     */
    public function set($sName, $mValue)
    {
        $aArr = &$_SESSION;
        foreach ($this->getPath($sName) as $sKey) {
            if (!isset($aArr[$sKey]) || !is_array($aArr[$sKey]))
                $aArr[$sKey] = array();
            $aArr = &$aArr[$sKey];
        }

        $aArr = $mValue;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Checks if attribute with specified name is exists on session.
     *
     * @param string $sName The name of attribute.
     *
     * @return boolean
     */
    public function contains($sName)
    {
        $aArr = &$_SESSION;
        foreach ($this->getPath($sName) as $sKey)
            if (!is_array($aArr) || !isset($aArr[$sKey]))
                return false;
            else
                $aArr = &$aArr[$sKey];

        return true;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Cleans value of attribute with specified name.
     *
     * @param string $sName The name of attribute for cleaning.
     *
     * @return mixed The cleaned value of attribute.
     */
    public function clean($sName)
    {
        $aArr = &$_SESSION;
        $aPathItems = $this->getPath($sName);
        $nCountPathItems = count($aPathItems);
        for ($i = 0; $i < $nCountPathItems - 1; $i++) {
            if (!is_array($aArr) || !isset($aArr[$aPathItems[$i]]))
                return null;
            else
                $aArr = &$aArr[$aPathItems[$i]];
        }

        $mValue = $aArr[$aPathItems[$i]];
        unset($aArr[$aPathItems[$i]]);
        return $mValue;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Cleans all data in session.
     *
     * @return void
     */
    public function cleanAll()
    {
        $_SESSION = array();

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------