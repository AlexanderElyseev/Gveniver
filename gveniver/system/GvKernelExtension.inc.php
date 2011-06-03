<?php
/**
 * File contains base abstract kernel extension class.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

GvInclude::instance()->includeFile('system/extension/ExtensionData.inc.php');

/**
 * Base abstract kernel extension class.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 * @abstract
 */
abstract class GvKernelExtension
{
    /**
     * Current kernel of extension.
     *
     * @var GvKernel
     */
    protected $cKernel;
    //-----------------------------------------------------------------------------

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
     *
     * @param GvKernel $cKernel Current kernel.
     */
    public function __construct(GvKernel $cKernel)
    {
        // Save current kernel.
        $this->cKernel = $cKernel;
        
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

    /**
     * Add resource to the profile.
     *
     * @return void
     */
    public function addResource()
    {
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Add handler to the profile.
     *
     * @param ExtensionHandler $cHandler Handler to add.
     * 
     * @return void
     */
    public function addHandler(ExtensionHandler $cHandler)
    {
    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------