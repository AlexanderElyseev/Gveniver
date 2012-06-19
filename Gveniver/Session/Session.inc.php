<?php
/**
 * File contains class of session.
 *
 * @category  Gveniver
 * @package   Session
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

namespace Gveniver\Session;

/**
 * Class of session.
 *
 * @category  Gveniver
 * @package   Session
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class Session extends \Gveniver\BaseObject
{
    /**
     * Current session storage.
     *
     * @var Storage\BaseSessionStorage
     */
    private $_cStorage;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Creates new instance of {@see \Gveniver\Session\Session}.
     *
     * @param Storage\BaseSessionStorage $cStorage Current session storage.
     */
    public function __construct(Storage\BaseSessionStorage $cStorage)
    {
        parent::__construct($cStorage->getApplication());

        $this->_cStorage = $cStorage;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Getter for the identifier of the session.
     *
     * @return string
     */
    public function getId()
    {
        return $this->_cStorage->getId();

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Setter for the identifier of the session.
     *
     * @param string $sId Identifier of session to set.
     *
     * @return void
     */
    public function setId($sId)
    {
        $this->_cStorage->setId($sId);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Starts the session.
     *
     * @return void
     */
    public function start()
    {
        $this->_cStorage->start();

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Migrates the current session to a new session id while maintaining all session attributes.
     *
     * @return void
     */
    public function migrate()
    {
        $this->_cStorage->migrate();

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Invalidates the current session. Clears all session attributes.
     *
     * @return void
     */
    public function invalidate()
    {
        $this->_cStorage->invalidate();

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
        $aData = $this->_cStorage->get();
        $aArr = &$aData;
        $aPathItems = $this->_getPath($sName);
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
        return $this->_cStorage->get();

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Sets value of attribute with specified name.
     *
     * @param string $sName  The name of attribute for saving.
     * @param mixed  $mValue The value of attribute for saving.
     *
     * @return void
     */
    public function set($sName, $mValue)
    {
        $aData = $this->_cStorage->get();
        $aArr = &$aData;
        foreach ($this->_getPath($sName) as $sKey) {
            if (!isset($aArr[$sKey]) || !is_array($aArr[$sKey]))
                $aArr[$sKey] = array();
            $aArr = &$aArr[$sKey];
        }

        $aArr = $mValue;
        $this->_cStorage->set($aData);

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
        $aData = $this->_cStorage->get();
        $aArr = &$aData;
        foreach ($this->_getPath($sName) as $sKey)
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
        $aData = $this->_cStorage->get();
        $aArr = &$aData;

        $aPathItems = $this->_getPath($sName);
        $nCountPathItems = count($aPathItems);
        for ($i = 0; $i < $nCountPathItems - 1; $i++) {
            if (!is_array($aArr) || !isset($aArr[$aPathItems[$i]]))
                return null;
            else
                $aArr = &$aArr[$aPathItems[$i]];
        }

        $mValue = isset($aArr[$aPathItems[$i]]) ? $aArr[$aPathItems[$i]] : null;
        unset($aArr[$aPathItems[$i]]);
        $this->_cStorage->set($aData);
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
        $this->_cStorage->clean();

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Build path to attribute by specified name.
     * Used for access to multydimensional data.
     *
     * @param string $sName Attribute name for converting to path.
     *
     * @return array
     */
    private function _getPath($sName)
    {
        // Transform /a/b -> a/b
        if (mb_strlen($sName) > 0 && mb_substr($sName, 0, 1) == \Gveniver\Kernel\Module\SessionModule::PATH_SEPARATOR)
            $sName = mb_substr($sName, 1);

        return explode(\Gveniver\Kernel\Module\SessionModule::PATH_SEPARATOR, $sName);

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------