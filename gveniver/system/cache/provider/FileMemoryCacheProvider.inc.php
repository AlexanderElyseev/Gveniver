<?php
/**
 * File contains cache provider class with files and memory.
 *
 * @category  Gveniver
 * @package   Cache
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

GvInclude::instance()->includeFile('system/cache/provider/FileCacheProvider.inc.php');

/**
 * Cache provider class with files and memory.
 *
 * @category  Gveniver
 * @package   Cache
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class FileMemoryCacheProvider extends FileCacheProvider
{
    /**
     * Cache of data in memory.
     * 
     * @var array
     */
    private $_aMemoryData = array();
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Load data form cache.
     *
     * @param string $sCacheId      Identifier of cache.
     * @param string $sCacheGroupId Identifier of cache group.
     * @param mixed  &$cRef         Reference variable for loading cached data.
     *
     * @return boolean True on success loading
     */
    public function get($sCacheId, $sCacheGroupId, &$cRef)
    {
        // Try to load from memory.
        if (array_key_exists($sCacheGroupId, $this->_aMemoryData) && is_array($this->_aMemoryData[$sCacheGroupId])) {
            if (array_key_exists($sCacheId, $this->_aMemoryData[$sCacheGroupId])) {
                $cRef = $this->_aMemoryData[$sCacheGroupId][$sCacheId];
                return true;
            }
        }

        // Load data from file cache.
        $mData = null;
        $bResult = parent::get($sCacheId, $sCacheGroupId, $mData);

        // Save to memory on success loading.
        if ($bResult)
            $this->_aMemoryData[$sCacheGroupId][$sCacheId] = is_object($mData) ? clone $mData : $mData;

        // Return result.
        if ($bResult)
            $cRef = $mData;

        return $bResult;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Save data to cache.
     *
     * @param mixed  $mData         Data to save.
     * @param strin  $sCacheId      Identifier of cache.
     * @param string $sCacheGroupId Identifier of cache group.
     * @param int    $nTtl          Time to live for cache.
     *
     * @return boolean True on success.
     */
    public function set($mData, $sCacheId, $sCacheGroupId, $nTtl)
    {
        // Save to memory.
        $this->_aMemoryData[$sCacheGroupId][$sCacheId] = is_object($mData) ? clone $mData : $mData;

        // Save to file.
        return parent::set($mData, $sCacheId, $sCacheGroupId, $nTtl);
        
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Flush cache data.
     *
     * @param string $sCacheGroupId Identifier of cache group.
     *
     * @return boolean True on success.
     */
    public function flush($sCacheGroupId)
    {
        // Flush memory cache.
        $this->_aMemoryData[$sCacheGroupId] = array();

        // Flush file cache.
        return parent::flush($sCacheGroupId);

    } // End function
    //-----------------------------------------------------------------------------
    
} // End class
//-----------------------------------------------------------------------------