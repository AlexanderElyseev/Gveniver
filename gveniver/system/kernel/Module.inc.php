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

namespace Gveniver\Kernel;
\Gveniver\Loader::i('system/exception/Exception.inc.php');

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
abstract class Module
{
    /**
     * Reference to current kernel.
     *
     * @var Kernel
     */
    protected $cKernel;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Constructor of {@see Module} class.
     * Initialize new instance of profile by kernel.
     *
     * @param Kernel $cKernel Kernel of profile.
     */
    public final function __construct(Kernel $cKernel)
    {
        $this->cKernel = $cKernel;
        if (!$this->init())
            throw new \Gveniver\Exception\Exception(
                sprintf('Initialization of module "%s" failed.', get_class($this))
            );

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