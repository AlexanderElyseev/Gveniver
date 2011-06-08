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
     * @var GvKernel
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
     * @param GvKernel $cKernel  Current kernel.
     * @param array    $aOptions Options for provider.
     */
    public function __construct(GvKernel $cKernel, array $aOptions)
    {
        $this->cKernel = $cKernel;
        $this->aOptions = $aOptions;

        // Try to connect.
        if (!$this->connect())
            throw new GvException('Error in connection to data source.');

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