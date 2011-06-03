<?php
/**
 * File contains base abstract kernel profile class.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

/**
 * Base abstract kernel profile class.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 * @abstract
 */
abstract class GvKernelModule
{
    /**
     * Reference to current kernel.
     *
     * @var GvKernel
     */
    protected $cKernel;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Constructor of {@see GvKernelModule} class.
     * Initialize new instance of profile by kernel.
     *
     * @param GvKernel $cKernel Kernel of profile.
     */
    public final function __construct(GvKernel $cKernel)
    {
        $this->cKernel = $cKernel;
        if (!$this->init())
            throw new GvException('Initialization of module failed.');

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Full initialization of kernel module.
     *
     * @return bool True on success.
     * @abstract
     */
    protected abstract function init();
    //-----------------------------------------------------------------------------
    
} // End class
//-----------------------------------------------------------------------------