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
     * Load data form cache.
     *
     * @param string $sCacheId   Identifier of cache.
     * @param string $sNamespace Namespace of cache.
     * @param mixed  &$cRef      Reference variable for loading cached data.
     *
     * @return boolean True on success loading
     */
    public function get($sCacheId, $sNamespace, &$cRef)
    {
        // Try to load from memory.
        if (array_key_exists($sNamespace, $this->_aMemoryData) && is_array($this->_aMemoryData[$sNamespace])) {
            if (array_key_exists($sCacheId, $this->_aMemoryData[$sNamespace])) {
                $cRef = $this->_aMemoryData[$sNamespace][$sCacheId];
                return true;
            }
        }

        // Load data from file cache.
        $mData = null;
        $bResult = parent::get($sCacheId, $sNamespace, $mData);

        // Save to memory on success loading.
        if ($bResult)
            $this->_aMemoryData[$sNamespace][$sCacheId] = is_object($mData) ? clone $mData : $mData;

        // Return result.
        if ($bResult)
            $cRef = $mData;

        return $bResult;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Save data to cache.
     *
     * @param mixed  $mData      Data to save.
     * @param string $sCacheId   Identifier of cache.
     * @param string $sNamespace Namespace of cache.
     * @param array  $aTags      List of tags for this cache record.
     * @param int    $nTtl       Time to live for cache.
     *
     * @return boolean True on success.
     */
    public function set($mData, $sCacheId, $sNamespace, array $aTags, $nTtl)
    {
        // Save data to memory.
        $this->_aMemoryData[$sNamespace][$sCacheId] = is_object($mData) ? clone $mData : $mData;

        // Save tag meta information.
        foreach ($aTags as $sTag) {
            if (!isset($this->_aMemoryTags[$sTag]) || !is_array($this->_aMemoryTags[$sTag]))
                $this->_aMemoryTags[$sTag] = array();
            $this->_aMemoryTags[$sTag][] = array($sNamespace, $sCacheId);
        }

        // Save to file.
        return parent::set($mData, $sCacheId, $sNamespace, $aTags, $nTtl);
        
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Method cleans cache data by specified parameters.
     *
     * @param string $sNamespace Namespace of cache.
     * @param string $sCacheId   Identifier of cache. If it is specified, clean only record with specified identifier.
     *                           Otherwise, clean all namespace.
     *
     * @return boolean True on success.
     */
    public function clean($sNamespace, $sCacheId = null)
    {
        // Cleaning memory cache.
        $this->_aMemoryData[$sNamespace] = array();

        // Cleaning file cache.
        return parent::clean($sNamespace, $sCacheId);

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
     * Method cleans cache data by specified tags.
     *
     * @param array $aTags List of tags for cleaning.
     *
     * @throws \Gveniver\Exception\NotImplementedException
     * @return boolean True on success.
     */
    public function cleanByTags(array $aTags)
    {
        $bRet = true;
        foreach ($aTags as $sTag)
            if (array_key_exists($sTag, $this->_aMemoryTags))
                foreach ($this->_aMemoryTags as $aTagItem)
                    $bRet = $bRet && $this->clean($aTagItem[0], $aTagItem[1]);

        return $bRet;

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------