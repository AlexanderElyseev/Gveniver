<?php

GvInclude::instance()->includeFile('gveniver/system/data/provider/DataProvider.inc.php');

class MongoDbDataProvider extends DataProvider
{
    /**
     * MongoDB object.
     * 
     * @var Mongo
     */
    private $_cMongo;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Connect to data source.
     *
     * @return boolean True on success connection.
     */
    protected function connect()
    {
        // Check for existing MongoDb PHP extension.
        if (!class_exists('Mongo')) {
            $this->cKernel->trace->addLine('[%s] MongoDb PHP extension is not installed.', __CLASS__);
            return false;
        }

        // Create new MongoDb instance.
        $this->_cMongo = new Mongo();
        if (!$this->_cMongo)
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
        return $this->_cMongo;
        
    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------