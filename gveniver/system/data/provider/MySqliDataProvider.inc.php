<?php

GvInclude::instance()->includeFile('gveniver/system/data/provider/DataProvider.inc.php');

class MySqliDataProvider extends DataProvider
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
        // Check for existing MySqli PHP extension.
        if (!class_exists('mysqli')) {
            $this->cKernel->trace->addLine('[%s] MySqli PHP extension is not installed.', __CLASS__);
            return false;
        }

        // Load connection parameters.
        $sHost = isset($this->aOptions['Host']) ? $this->aOptions['Host'] : array();
        $sUser = isset($this->aOptions['User']) ? $this->aOptions['User'] : array();
        $sPassword = isset($this->aOptions['Password']) ? $this->aOptions['Password'] : array();
        $sDb = isset($this->aOptions['Database']) ? $this->aOptions['Database'] : array();

        // Try to connect.
        $this->_cConnection = new mysqli($sHost, $sUser, $sPassword, $sDb);
        if ($this->_cConnection->connect_error)
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