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

    /**
     * Configuration parameters of storage.
     *
     * @var array
     */
    private $_aConfig = array();
    //-----------------------------------------------------------------------------

    /**
     * Creates new instance of {@see \Gveniver\Session\Storage\BaseSessionStorage}.
     *
     * @param \Gveniver\Kernel\Application $cApp    Current application.
     * @param string                       $sId     Identifier of session.
     * @param array                        $aConfig Configuration of storage.
     */
    public final function __construct(\Gveniver\Kernel\Application $cApp, $sId = null, array $aConfig = array())
    {
        parent::__construct($cApp);

        $this->_sId = $sId;
        $this->_aConfig = $aConfig;

        $this->init();

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Getter for array with configuration parameters of storage.
     *
     * @return array
     */
    protected function getConfig()
    {
        return $this->_aConfig;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Getter for the identifier of the session.
     *
     * @return string
     */
    public function getId()
    {
        return $this->_sId;

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
        $this->_sId = $sId;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Template method for initialization of provider. Is called from constructor.
     * Should be overriden for storage configuration.
     * By default do nothing.
     *
     * @return void
     */
    protected function init()
    {
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Starts the session with specified identifier.
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
     */
    public abstract function migrate();
    //-----------------------------------------------------------------------------

    /**
     * Invalidates the current session. Clears all session attributes.
     *
     * @return void
     */
    public abstract function invalidate();
    //-----------------------------------------------------------------------------

    /**
     * Gets session data from the persistence.
     *
     * @return array
     * @abstract
     */
    public abstract function get();
    //-----------------------------------------------------------------------------

    /**
     * Sets session data at the persistence.
     *
     * @param array $aSession Data for saving.
     *
     * @return void
     * @abstract
     */
    public abstract function set(array $aSession);
    //-----------------------------------------------------------------------------

    /**
     * Cleans all session data at the persistence.
     *
     * @return void
     * @abstract
     */
    public abstract function clean();
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------