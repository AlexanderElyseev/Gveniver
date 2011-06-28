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

namespace Gveniver\Kernel;
\Gveniver\Loader::i('system/exception/Exception.inc.php');

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
abstract class Module
{
    /**
     * Reference to current kernel.
     *
     * @var Application
     */
    private $_cApplication;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Constructor of {@see Module} class.
     * Initialize new instance of profile by kernel.
     *
     * @param Application $cApplication Current application.
     */
    public final function __construct(Application $cApplication)
    {
        $this->_cApplication = $cApplication;
        if (!$this->init())
            throw new \Gveniver\Exception\Exception(
                sprintf('Initialization of module "%s" failed.', get_class($this))
            );

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Getter for current application.
     *
     * @return Application
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