<?php

namespace Gveniver\Kernel\Module;

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
     * @var \Gveniver\Session\Storage\BaseSessionStorage
     */
    private $_cStorage;
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

        $this->_cStorage = new \Gveniver\Session\Storage\NativeSessionStorage();

        $this->getApplication()->trace->addLine('[%s] Init successful.', __CLASS__);
        return true;

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
     * Gets the identifier of the session.
     *
     * @return string
     */
    public function getId()
    {
        $this->_cStorage->getId();

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
        $this->_cStorage->setId($sId);

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
        return $this->_cStorage->get($this->_buildName($mName), $mDefault);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Returns all data in session.
     *
     * @return array
     */
    public function getAll()
    {
        return $this->_cStorage->getAll();

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
        $this->_cStorage->set($this->_buildName($mName), $mValue);

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
        return $this->_cStorage->contains($this->_buildName($mName));

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
        return $this->_cStorage->clean($this->_buildName($mName));

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Cleans all data in session.
     *
     * @return void
     */
    public function cleanAll()
    {
        $this->_cStorage->cleanAll();

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
            throw new \Gveniver\Exception\ArgumentException('Name of attribute can onlyb be array or string.');

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------