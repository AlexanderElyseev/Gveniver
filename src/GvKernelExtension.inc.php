<?php

GvKernelInclude::instance()->includeFile('src/system/extension/ExtensionData.inc.php');


abstract class GvKernelExtension
{
    /**
     * Data of current extension.
     *
     * @var ExtensionData
     */
    protected $cData;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Base extension constructor.
     * Load extension data with specific logic.
     */
    public function __construct()
    {
        // Load extension data.
        $this->cData = $this->loadData();
        if (!$this->cData)
            $this->cData = new ExtensionData();

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Template method for loading extension data.
     *
     * @return ExtensionData
     * @abstract
     */
    protected abstract function loadData();
    //-----------------------------------------------------------------------------

    /**
     * Query to extension.
     *
     * @param string $sAction Name of action handler.
     * @param array  $aParams Arguments to extension.
     *
     * @return mixed
     * @abstract
     */
    public abstract function query($sAction, $aParams = array());
    //-----------------------------------------------------------------------------
    
    public function addResource()
    {
    } // End function
    //-----------------------------------------------------------------------------

    public function addHandler(ExtensionHandler $cHandler)
    {
    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------