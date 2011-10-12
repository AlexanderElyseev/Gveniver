<?php
/**
 * File contains base abstract application profile class.
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
 * Base abstract application profile class.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 * @abstract
 */
abstract class BaseModule
{
    /**
     * Reference to current kernel.
     *
     * @var \Gveniver\Kernel\Application
     */
    private $_cApplication;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Constructor of {@see Module} class.
     * Initialize new instance of profile by kernel.
     *
     * @param \Gveniver\Kernel\Application $cApplication Current application.
     */
    public final function __construct(\Gveniver\Kernel\Application $cApplication)
    {
        $this->_cApplication = $cApplication;
        if (!$this->init())
            throw new \Gveniver\Exception\BaseException(
                sprintf('Initialization of module "%s" failed.', get_class($this))
            );

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Getter for current application.
     *
     * @return \Gveniver\Kernel\Application
     */
    public function getApplication()
    {
        return $this->_cApplication;

    } // End function
    //-----------------------------------------------------------------------------
    
    /**
     * Full initialization of module.
     *
     * @return bool True on success.
     * @abstract
     */
    protected abstract function init();
    //-----------------------------------------------------------------------------
    
} // End class
//-----------------------------------------------------------------------------