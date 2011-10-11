<?php
/**
 * File contains class for data provider over MongoDb.
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
 * Class for data provider over MongoDb.
 *
 * @category  Gveniver
 * @package   Template
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
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
            $this->getApplication()->trace->addLine('[%s] MongoDb PHP extension is not installed.', __CLASS__);
            return false;
        }

        // Create new MongoDb instance.
        $this->_cMongo = new \Mongo();
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