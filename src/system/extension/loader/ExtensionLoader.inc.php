<?php
/**
 *
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

/**
 *
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
abstract class ExtensionLoader
{
    /**
     * Current kernel.
     *
     * @var GvKernel
     */
    protected $cKernel;
    //-----------------------------------------------------------------------------

    /**
     * @var array
     */
    private $_aExtensions = array();
    //-----------------------------------------------------------------------------

    /**
     * @var array
     */
    private $_aNameHash = array();
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Base class constructor.
     * Initialize member fields.
     *
     * @param GvKernel $cKernel Current kernel.
     *
     * @return void
     */
    public function __construct(GvKernel $cKernel)
    {
        $this->cKernel = $cKernel;
                
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Loading extension by name.
     *
     * @param string $sExtensionName Name of extension for loading.
     * 
     * @return GvKernelExtension|boolean
     */
    public function get($sExtensionName)
    {
        $this->cKernel->trace->addLine('[%s] Loading extension ("%s").', __CLASS__, $sExtensionName);

        // First, try to load extension from cache by name.
        if (array_key_exists($sExtensionName, $this->_aNameHash)) {
            $this->cKernel->trace->addLine('[%s] Extension ("%s") loaded from cache.', __CLASS__, $sExtensionName);
            return $this->_aExtensions[$this->_aNameHash[$sExtensionName]];
        }

        // Load extension.
        $cExt = $this->load($sExtensionName);
        if (!$cExt) {
            $this->cKernel->trace->addLine('[%s] Extension ("%s") not loaded.', __CLASS__, $sExtensionName);
            return null;
        }

        $this->cKernel->trace->addLine('[%s] Extension ("%s") successfully loaded.', __CLASS__, $sExtensionName);
        
        // Save loaded extension to cache.
        $nIndex = count($this->_aExtensions);
        $this->_aExtensions[$nIndex] = 1;
        $this->_aNameHash[$sExtensionName] = $nIndex;

        // Return result.
        return $cExt;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Template method for direct loading of extension by name.
     * 
     * @param string $sExtensionName Name of extension for loading.
     *
     * @return GvKernelExtension Returns null on error.
     * @abstract
     */
    protected abstract function load($sExtensionName);
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------