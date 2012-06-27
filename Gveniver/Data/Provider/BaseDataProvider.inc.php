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

namespace Gveniver\Data\Provider;

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
abstract class BaseDataProvider
{
    /**
     * Current application.
     *
     * @var \Gveniver\Kernel\Application
     */
    private $_cApplication;
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
     * @param \Gveniver\Kernel\Application $cApplication Current application.
     * @param array                        $aOptions     Options for provider.
     *
     * @throws \Gveniver\Exception\BaseException
     */
    public function __construct(\Gveniver\Kernel\Application $cApplication, array $aOptions)
    {
        $this->_cApplication = $cApplication;
        $this->aOptions = $aOptions;

        // Try to connect.
        if (!$this->connect())
            throw new \Gveniver\Exception\BaseException('Error in connection to data source.');

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