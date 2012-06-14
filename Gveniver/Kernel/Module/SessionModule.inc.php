<?php
/**
 * File contains session module class.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

namespace Gveniver\Kernel\Module;

/**
 * Session module class.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class SessionModule extends BaseModule
{
    /**
     * Path separator for multidimensional names of attributes.
     *
     * @var string
     */
    const PATH_SEPARATOR = '/';
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Current session storage.
     *
     * @var \Gveniver\Session\Session
     */
    private $_cSession;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Full initialization of module.
     *
     * @return bool True on success.
     */
    protected function init()
    {
        $this->getApplication()->trace->addLine('[%s] Init.', __CLASS__);

        // Load configuration.
        $aConfig = $this->getApplication()->getConfig()->get('Module/SessionModule');
        if ($aConfig && isset($aConfig['StorageClass'])) {

            $aArgs = isset($aConfig['Args']) && is_array($aConfig['Args']) ? $aConfig['Args'] : array();
            $sClassName = '\\Gveniver\Session\\Storage\\'.$aConfig['StorageClass'];

            $this->getApplication()->trace->addLine('[%s] Initialization using configuration (with "%s" as storage).', __CLASS__, $sClassName);

            if (!class_exists($sClassName) || !is_subclass_of($sClassName, '\\Gveniver\\Session\\Storage\\BaseSessionStorage')) {
                $this->getApplication()->trace->addLine('[%s] Session storage class ("%s") is not exist or it do not extend base storage class.', __CLASS__, $sClassName);
                $this->_initializeNativeStorage();
            } else {
                try {
                    $cStorage = new $sClassName($this->getApplication(), null, $aArgs);
                    $this->_cSession = new \Gveniver\Session\Session($cStorage);
                } catch (\Gveniver\Exception\BaseException $cEx) {
                    $this->getApplication()->trace->addLine('[%s] Exception in create session storage instance ("%s"): "%s".', __CLASS__, $sClassName, $cEx->getMessage());
                    $this->_initializeNativeStorage();
                }
            }
        } else
            $this->_initializeNativeStorage();

        $this->getApplication()->trace->addLine('[%s] Init successful.', __CLASS__);
        return true;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Initializes module by native session storage.
     *
     * @return void
     */
    private function _initializeNativeStorage()
    {
        $this->getApplication()->trace->addLine('[%s] Initialization using native session storage.', __CLASS__);

        $cStorage = new \Gveniver\Session\Storage\NativeSessionStorage($this->getApplication());
        $this->_cSession = new \Gveniver\Session\Session($cStorage);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Starts the session.
     *
     * @return void
     */
    public function start()
    {
        $this->_cSession->start();

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Migrates the current session to a new session id while maintaining all session attributes.
     *
     * @return void
     */
    public function migrate()
    {
        $this->_cSession->migrate();

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Invalidates the current session. Clears all session attributes. Migrates to new session.
     *
     * @return void
     */
    public function invalidate()
    {
        $this->_cSession->invalidate();

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Gets the identifier of the session.
     *
     * @return string
     */
    public function getId()
    {
        $this->_cSession->getId();

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
        $this->_cSession->setId($sId);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Returns value of attribute with specified name.
     * If attribute is not exist, returns default value.
     *
     * @param string|array $mName    The name of attribute for saving.
     * @param mixed        $mDefault The default value if not found.
     *
     * @return mixed Value of attribute.
     */
    public function get($mName, $mDefault = null)
    {
        return $this->_cSession->get($this->_buildName($mName), $mDefault);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Returns all data in session.
     *
     * @return array
     */
    public function getAll()
    {
        return $this->_cSession->getAll();

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Sets value of attribute with specified name.
     *
     * @param string|array $mName  The name of attribute for saving.
     * @param mixed        $mValue The value of attribute for saving.
     *
     * @return void
     */
    public function set($mName, $mValue)
    {
        $this->_cSession->set($this->_buildName($mName), $mValue);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Checks if attribute with specified name is exists on session.
     *
     * @param string|array $mName The name of attribute.
     *
     * @return boolean
     */
    public function contains($mName)
    {
        return $this->_cSession->contains($this->_buildName($mName));

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Cleans value of attribute with specified name.
     *
     * @param string|array $mName The name of attribute for cleaning.
     *
     * @return mixed The cleaned value of attribute.
     */
    public function clean($mName)
    {
        return $this->_cSession->clean($this->_buildName($mName));

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Cleans all data in session.
     *
     * @return void
     */
    public function cleanAll()
    {
        $this->_cSession->cleanAll();

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Builds name for multidimensional attributes.
     *
     * @param mixed $mName The parts of name.
     *
     * @throws \Gveniver\Exception\ArgumentException Throws if name has incorrect type.
     * @return string
     */
    private function _buildName($mName)
    {
        if (is_array($mName))
            return implode(self::PATH_SEPARATOR, $mName);
        elseif (is_string($mName))
            return $mName;
        else
            throw new \Gveniver\Exception\ArgumentException('The name of the attribute can only be an array or a string.');

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------