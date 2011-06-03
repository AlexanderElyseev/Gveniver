<?php
/**
 * File contains class for data provider over MySql.
 *
 * @category  Gveniver
 * @package   Template
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */


GvInclude::instance()->includeFile('system/data/provider/DataProvider.inc.php');

/**
 * Class for data provider over MySql.
 *
 * @category  Gveniver
 * @package   Template
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class MySqlDataProvider extends DataProvider
{
    /**
     * MySql connection link.
     *
     * @var resource
     */
    private $_cConnection;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Connect to data source.
     *
     * @return boolean True on success connection.
     */
    protected function connect()
    {
        // Check for existing MySql PHP extension.
        if (!function_exists('mysql_connect')) {
            $this->cKernel->trace->addLine('[%s] MySql PHP extension is not installed.', __CLASS__);
            return false;
        }

        // Load connection parameters.
        $sHost = isset($this->aOptions['Host']) ? $this->aOptions['Host'] : array();
        $sUser = isset($this->aOptions['User']) ? $this->aOptions['User'] : array();
        $sPassword = isset($this->aOptions['Password']) ? $this->aOptions['Password'] : array();
        $sDb = isset($this->aOptions['Database']) ? $this->aOptions['Database'] : array();

        // Try to connect.
        $this->_cConnection = mysql_connect($sHost, $sUser, $sPassword);
        if (!$this->_cConnection)
            return false;

        // Try to select database.
        if (!mysql_select_db($sDb, $this->_cConnection))
            return false;
        
        return true;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Returns current connection.
     *
     * @return mixed
     */
    public function getConnection()
    {
        return $this->_cConnection;
        
    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------