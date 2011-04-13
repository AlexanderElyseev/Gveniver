<?php

GvKernelInclude::instance()->includeFile('gveniver/system/data/provider/DataProvider.inc.php');

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
            $this->_cPdo = new PDO($sDsn, $sUser, $sPassword, $aOptions);
            return true;
        } catch (Exception $cEx) {
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