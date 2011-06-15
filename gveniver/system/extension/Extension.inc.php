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

namespace Gveniver\Extension;

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
abstract class Extension
{
    /**
     * Configuration of extension.
     *
     * @var \Gveniver\Config
     */
    private $_cConfig;
    //-----------------------------------------------------------------------------

    /**
     * Current kernel of extension.
     *
     * @var \Gveniver\Kernel\Kernel
     */
    protected $cKernel;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Base extension constructor.
     *
     * @param \Gveniver\Kernel\Kernel $cKernel Current kernel.
     */
    public function __construct(\Gveniver\Kernel\Kernel $cKernel)
    {
        $this->cKernel = $cKernel;
        $this->_cConfig = new \Gveniver\Config();

    } // End function
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
     * Getter for extension configuration.
     * 
     * @return \Gveniver\Config
     */
    public function getConfig()
    {
        return $this->_cConfig;
        
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Load resource of extension by name with spwcified locale.
     *
     * @param string $sResourceName Name of resource for load.
     * @param string $sLocale       Target locale of resource.
     *
     * @return mixed
     */
    public function getResource($sResourceName, $sLocale = null)
    {
        
    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------