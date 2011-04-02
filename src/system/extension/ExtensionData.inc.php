<?php

class ExtensionData
{
    /**
     * List of extension resources.
     *
     * @var array
     */
    private $_aResources = array();
    //-----------------------------------------------------------------------------

    /**
     * Export list of extension action handlers.
     * 
     * @var array
     */
    //private $_aExport;
    //-----------------------------------------------------------------------------

    /**
     * Description of extension.
     *
     * @var string
     */
    private $_sDescription;
    //-----------------------------------------------------------------------------

    /**
     * Version of extension.
     *
     * @var string
     */
    private $_sVersion;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Add resource to extension data.
     *
     * @param string $sName   Name of resource.
     * @param mixed  $mValue  Value of resource.
     * @param string $sLocale Locale of resource.
     *
     * @return void
     */
    public function addResource($sName, $mValue, $sLocale = 'default')
    {
        $this->_aResources[$sName][$sLocale] = $mValue;
        
    } // End function
    //-----------------------------------------------------------------------------
    
    /**
     * Returns resource of extension data.
     *
     * @param string $sName   Name of resource.
     * @param string $sLocale Locale of resource.
     *
     * @return mixed
     */
    public function getResource($sName, $sLocale = 'default')
    {
        if (isset($this->_aResources[$sName][$sLocale]))
            return $this->_aResources[$sName][$sLocale];

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Setter for extension description.
     *
     * @param string $sDescription Descrition of extension to set.
     * 
     * @return void
     */
    public function setDescription($sDescription)
    {
        $this->_sDescription = $sDescription;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Getter for extension description.
     * 
     * @return string
     */
    public function getDescription()
    {
        return $this->_sDescription;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Setter for extension version.
     *
     * @param string $sVersion Version of extension to set.
     *
     * @return void
     */
    public function setVersion($sVersion)
    {
        $this->_sVersion = $sVersion;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Getter for extension version.
     * 
     * @return string
     */
    public function getVersion()
    {
        return $this->_sVersion;

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------