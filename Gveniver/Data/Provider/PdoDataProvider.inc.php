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

namespace Gveniver\Data\Provider;

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
class PdoDataProvider extends BaseDataProvider
{
    /**
     * PDO object.
     *
     * @var \PDO
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
        if (!class_exists('\PDO')) {
            $this->getApplication()->trace->addLine('[%s] PDO PHP extension is not installed.', __CLASS__);
            return false;
        }

        // Load connection parameters.
        $sDsn = isset($this->aOptions['Dsn']) ? $this->aOptions['Dsn'] : null;
        $sUser = isset($this->aOptions['User']) ? $this->aOptions['User'] : null;
        $sPassword = isset($this->aOptions['Password']) ? $this->aOptions['Password'] : null;
        $aOptions = isset($this->aOptions['Options']) ? $this->aOptions['Options'] : array();
        $sInitCommand = isset($this->aOptions['InitCommand']) ? $this->aOptions['InitCommand'] : null;

        // Try to connect.
        try {
            $this->_cPdo = new \PDO($sDsn, $sUser, $sPassword, $aOptions);
        } catch (\PDOException $cEx) {
            return false;
        }

        //TODO: init command is hack!!! Need to check.
        // Execute initialization command.
        if ($sInitCommand)
            $this->_cPdo->prepare($sInitCommand)->execute();

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
        return $this->_cPdo;
        
    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------