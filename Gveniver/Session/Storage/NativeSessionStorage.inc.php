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
     * Starts the session.
     *
     * @return void
     */
    public function start()
    {
        $sId = $this->getId();
        if ($sId)
            session_id($sId);

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