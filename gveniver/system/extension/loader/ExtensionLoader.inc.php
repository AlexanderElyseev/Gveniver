<?php
/**
 * File contsins base abstract class for loader of extensions.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

namespace Gveniver\Extension;

/**
 * Base abstract class for loader of extensions.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 * @abstract
 */
abstract class ExtensionLoader
{
    /**
     * Current kernel.
     *
     * @var \Gveniver\Kernel\Kernel
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
     * @param \Gveniver\Kernel\Kernel $cKernel Current kernel.
     */
    public function __construct(\Gveniver\Kernel\Kernel $cKernel)
    {
        $this->cKernel = $cKernel;
                
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Loading extension by name.
     *
     * @param string $sExtensionName Name of extension for loading.
     * 
     * @return Extension|boolean
     */
    public function get($sExtensionName)
    {
        $this->cKernel->trace->addLine('[%s] Loading extension ("%s").', __CLASS__, $sExtensionName);

        // First, try to load extension from cache by name.
        if (array_key_exists($sExtensionName, $this->_aNameHash)) {
            $this->cKernel->trace->addLine('[%s] Extension ("%s") loaded from cache.', __CLASS__, $sExtensionName);
            $nId = $this->_aNameHash[$sExtensionName];
            return $this->_aExtensions[$nId];
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
        $this->_aExtensions[$nIndex] = $cExt;
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
     * @return Extension Returns null on error.
     * @abstract
     */
    protected abstract function load($sExtensionName);
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------