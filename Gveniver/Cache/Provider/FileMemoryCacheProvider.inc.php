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

namespace Gveniver\Cache\Provider;

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

    /**
     * Meta information about tags.
     *
     * @var array
     */
    private $_aMemoryTags = array();
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Method loads cached data by identifier.
     *
     * @param string $sCacheId The identifier of cached data.
     * @param mixed  &$cRef    Reference variable for loading cached data.
     *
     * @return boolean True on success loading.
     */
    public function get($sCacheId, &$cRef)
    {
        // Try to load from memory.
        if (array_key_exists($sCacheId, $this->_aMemoryData)) {
            if ($this->_aMemoryData[$sCacheId][1] && time() <= $this->_aMemoryData[$sCacheId][1]) {
                $cRef = $this->_aMemoryData[$sCacheId][0];
                return true;
            }
        }

        // Load data from file cache.
        $mData = null;
        $bResult = parent::get($sCacheId, $mData);

        // Save to memory on success loading.
        if ($bResult)
            $this->_aMemoryData[$sCacheId] = array(is_object($mData) ? clone $mData : $mData, null);

        // Return result.
        if ($bResult)
            $cRef = $mData;

        return $bResult;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Method saves data to the cache.
     *
     * @param mixed  $mData    Data for caching.
     * @param string $sCacheId The identifier of cached data.
     * @param array  $aTags    List of tags for this cache record.
     * @param int    $nTtl     Time to live for cache in seconds.
     *
     * @return boolean True on success.
     */
    public function set($mData, $sCacheId, array $aTags, $nTtl)
    {
        // Save data to memory.
        $this->_aMemoryData[$sCacheId] = array(is_object($mData) ? clone $mData : $mData, $nTtl ? time() + $nTtl : null);

        // Save tag meta information.
        foreach ($aTags as $sTag) {
            if (!isset($this->_aMemoryTags[$sTag]))
                $this->_aMemoryTags[$sTag] = array();

            $this->_aMemoryTags[$sTag][] = $sCacheId;
        }

        // Save to file.
        return parent::set($mData, $sCacheId, $aTags, $nTtl);
        
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Method cleans cached data by identifier.
     *
     * @param string $sCacheId The identifier of cached data.
     *
     * @return boolean True on success.
     */
    public function clean($sCacheId)
    {
        // Cleaning memory cache.
        unset($this->_aMemoryData[$sCacheId]);

        // Cleaning file cache.
        return parent::clean($sCacheId);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Method cleans all cache data.
     *
     * @return boolean True on success.
     */
    public function cleanAll()
    {
        // Cleaning memory cache.
        $this->_aMemoryData = array();

        // Cleaning file cache.
        return parent::cleanAll();

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Method cleans cached data by specified tags.
     *
     * @param array $aTags List of tags for cleaning.
     *
     * @return boolean True on success.
     */
    public function cleanByTags(array $aTags)
    {
        foreach ($aTags as $sTag)
            if (array_key_exists($sTag, $this->_aMemoryTags))
                foreach ($this->_aMemoryTags[$sTag] as $sTagItem)
                    $this->clean($sTagItem);

        // Cleaning file cache.
        return parent::cleanByTags($aTags);

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------