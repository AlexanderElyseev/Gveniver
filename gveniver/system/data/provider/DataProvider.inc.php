<?php
/**
 * File contains base abstract class for data provider.
 *
 * @category  Gveniver
 * @package   Template
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

namespace Gveniver\Data;

/**
 * Base abstract class for data provider.
 *
 * @category  Gveniver
 * @package   Template
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 * @abstract
 */
abstract class DataProvider
{
    /**
     * Current kernel.
     *
     * @var Kernel
     */
    protected $cKernel;
    //-----------------------------------------------------------------------------

    /**
     * Array of options for data provider.
     *
     * @var array
     */
    protected $aOptions;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Base constructor.
     * Initialize member fields.
     *
     * @param \Gveniver\Kernel\Kernel $cKernel  Current kernel.
     * @param array                   $aOptions Options for provider.
     */
    public function __construct(\Gveniver\Kernel\Kernel $cKernel, array $aOptions)
    {
        $this->cKernel = $cKernel;
        $this->aOptions = $aOptions;

        // Try to connect.
        if (!$this->connect())
            throw new \Gveniver\Exception\Exception('Error in connection to data source.');

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Connect to data source.
     *
     * @return boolean True on success connection.
     * @abstract
     */
    protected abstract function connect();
    //-----------------------------------------------------------------------------

    /**
     * Returns current connection.
     *
     * @return mixed
     * @abstract
     */
    public abstract function getConnection();
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------