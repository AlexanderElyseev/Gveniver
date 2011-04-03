<?php

GvKernelInclude::instance()->includeFile('src/system/data/provider/DataProvider.inc.php');

class MongoDbDataProvider extends DataProvider
{
    /**
     * Connect to data source.
     *
     * @return boolean True on success connection.
     */
    protected function connect()
    {
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Returns current connection.
     *
     * @return mixed
     */
    public function getConnection()
    {
    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------