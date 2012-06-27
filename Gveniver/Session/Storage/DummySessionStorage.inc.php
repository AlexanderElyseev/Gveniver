<?php
/**
 * File contains dummy session storage class for tests.
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
 * Dummy session storage class for tests.
 *
 * @category  Gveniver
 * @package   Session
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class DummySessionStorage extends BaseSessionStorage
{
    /**
     * Dummy session storage array.
     *
     * @var array
     */
    private $_aSession = array();
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Starts the session with specified identifier.
     *
     * @return void
     */
    public function start()
    {
        $this->_aSession = array();

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Migrates the current session to a new session id while maintaining all session attributes.
     *
     * @return void
     */
    public function migrate()
    {
        $this->setId(md5(uniqid(rand(), true)));

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Invalidates the current session. Clears all session attributes.
     *
     * @return void
     */
    public function invalidate()
    {
        unset($this->_aSession);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Gets session data from the persistence.
     *
     * @return array
     */
    public function get()
    {
        return $this->_aSession;

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
        $this->_aSession = $aSession;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Cleans all session data at the persistence.
     *
     * @return void
     */
    public function clean()
    {
        $this->_aSession = array();

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------