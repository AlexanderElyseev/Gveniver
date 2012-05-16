<?php
/**
 * File contains base abstract session storage class.
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
 * Base abstract session storage class.
 *
 * @category  Gveniver
 * @package   Session
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
abstract class BaseSessionStorage extends \Gveniver\BaseObject
{
    /**
     * Identifier of the session.
     *
     * @var string
     */
    private $_sId;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Starts the session.
     *
     * @return void
     * @abstract
     */
    public abstract function start();
    //-----------------------------------------------------------------------------

    /**
     * Migrates the current session to a new session id while maintaining all session attributes.
     *
     * @return void
     * @abstract
     */
    public abstract function migrate();
    //-----------------------------------------------------------------------------

    /**
     * Invalidates the current session. Clears all session attributes. Migrates to new session.
     *
     * @return void
     * @abstract
     */
    public abstract function invalidate();
    //-----------------------------------------------------------------------------

    /**
     * Gets the identifier of the session.
     *
     * @return string
     */
    public function getId()
    {
        return $this->_sId;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Sets the identifier of the session.
     *
     * @param string $sId Identifier of session to set.
     *
     * @return void
     */
    public function setId($sId)
    {
        $this->_sId = $sId;

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
     * @abstract
     */
    public abstract function get($sName, $mDefault = null);
    //-----------------------------------------------------------------------------

    /**
     * Returns all data in session.
     *
     * @return array
     * @abstract
     */
    public abstract function getAll();
    //-----------------------------------------------------------------------------

    /**
     * Sets value of attribute with specified name.
     *
     * @param string $sName  The name of attribute for saving.
     * @param mixed  $mValue The value of attribute for saving.
     *
     * @return void
     * @abstract
     */
    public abstract function set($sName, $mValue);
    //-----------------------------------------------------------------------------

    /**
     * Checks if attribute with specified name is exists on session.
     *
     * @param string $sName The name of attribute.
     *
     * @return boolean
     * @abstract
     */
    public abstract function contains($sName);
    //-----------------------------------------------------------------------------

    /**
     * Cleans value of attribute with specified name.
     *
     * @param string $sName The name of attribute for cleaning.
     *
     * @return mixed The cleaned value of attribute.
     * @abstract
     */
    public abstract function clean($sName);
    //-----------------------------------------------------------------------------

    /**
     * Cleans all data in session.
     *
     * @return void
     * @abstract
     */
    public abstract function cleanAll();
    //-----------------------------------------------------------------------------

    /**
     * Build path to attribute by specified name.
     * Used for access to multydimensional data.
     *
     * @param string $sName Attribute name for converting to path.
     *
     * @return array
     */
    protected function getPath($sName)
    {
        return explode(\Gveniver\Kernel\Module\SessionModule::PATH_SEPARATOR, $sName);

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------