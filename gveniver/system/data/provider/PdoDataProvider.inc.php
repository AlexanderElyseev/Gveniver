<?php
/**
 * File contains class for data provider over PDO system.
 *
 * @category  Gveniver
 * @package   Template
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */


namespace Gveniver\Data;
\Gveniver\Loader::i('system/data/provider/DataProvider.inc.php');

/**
 * Class for data provider over PDO system.
 *
 * @category  Gveniver
 * @package   Template
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class PdoDataProvider extends DataProvider
{
    /**
     * PDO object.
     *
     * @var PDO
     */
    private $_cPdo;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Connect to data source.
     *
     * @return boolean True on success connection.
     */
    protected function connect()
    {
        // Check for existing PDO PHP extension.
        if (!function_exists('PDO')) {
            $this->cKernel->trace->addLine('[%s] PDO PHP extension is not installed.', __CLASS__);
            return false;
        }

        // Load connection parameters.
        $sDsn = isset($this->aOptions['Dsn']) ? $this->aOptions['Dsn'] : array();
        $sUser = isset($this->aOptions['User']) ? $this->aOptions['User'] : array();
        $sPassword = isset($this->aOptions['Password']) ? $this->aOptions['Password'] : array();
        $aOptions = isset($this->aOptions['Options']) ? $this->aOptions['Options'] : array();

        // Try to connect.
        try {
            $this->_cPdo = new \PDO($sDsn, $sUser, $sPassword, $aOptions);
            return true;
        } catch (\Gveniver\Exception\Exception $cEx) {
            return false;
        }

    } // End function
    //-----------------------------------------------------------------------------
    
    /**
     * Returns current connection.
     *
     * @return mixed
     */
    public function getConnection()
    {
        return $this->_cPdo;
        
    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------