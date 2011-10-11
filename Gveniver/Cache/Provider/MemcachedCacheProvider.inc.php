<?php
/**
 * File contains cache provider class for memcached system.
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
 * Cache provider class for memcached system.
 *
 * @category  Gveniver
 * @package   Cache
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class MemcachedCacheProvider extends CacheProvider
{
    /**
     * memcached object.
     * 
     * @var Memcached
     */
    private $_cMemcace;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------
    
    /**
     * Class constructor.
     *
     * @param \Gveniver\Kernel\Application $cApplication Current application.
     * @param array                        $aOptions     Options for cache provider.
     */
    public function __construct(\Gveniver\Kernel\Application $cApplication, array $aOptions)
    {
        // Use parent constructor.
        parent::__construct($cApplication, $aOptions);

        // Check for existing Mecached PHP extension.
        if (!class_exists('Memcache'))
            throw new \Gveniver\Exception\Exception('Memcache PHP extension not loaded.');

        $this->_cMemcace = new \Memcache();

        // Adding  servers.
        if (!isset($this->aOptions['Servers']))
            throw new \Gveniver\Exception\Exception('Memcache servers not loaded.');

        foreach ($this->aOptions['Servers'] as $aServerData) {
            $sServerHost = isset($aServerData['Host']) ? $aServerData['Host'] : null;
            if (!$sServerHost)
                continue;

            $this->_cMemcace->addserver($sServerHost);
        }

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
        $aMeta = $this->_cMemcace->get($this->_getMetaCacheId($sCacheGroupId));
        if (!is_array($aMeta))
            return true;

        foreach ($aMeta as $sDataCacheId)
            $this->_cMemcace->delete($sDataCacheId);

        return true;

    } // End function
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
        // Load data from memcache.
        $mData = $this->_cMemcace->get($this->_getDataCacheId($sCacheId, $sCacheGroupId));

        // Return result.
        $bResult = $mData !== false;
        if ($bResult)
            $cRef = $mData;

        return $bResult;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Save data to cache.
     *
     * @param mixed  $mData         Data to save.
     * @param string $sCacheId      Identifier of cache.
     * @param string $sCacheGroupId Identifier of cache group.
     * @param int    $nTtl          Time to live for cache.
     *
     * @return boolean True on success.
     */
    public function set($mData, $sCacheId, $sCacheGroupId, $nTtl)
    {
        $sDataCacheId = $this->_getDataCacheId($sCacheId, $sCacheGroupId);

        // Load group meta data. Update and save.
        $sMetaCacheId = $this->_getMetaCacheId($sCacheGroupId);
        $aMeta = $this->_cMemcace->get($sMetaCacheId);
        if (!is_array($aMeta)) {
            $aMeta = array($sDataCacheId);
            $this->_cMemcace->set($sMetaCacheId, $aMeta);
        } elseif (!in_array($sDataCacheId, $aMeta)) {
            array_push($aMeta, $sDataCacheId);
            $this->_cMemcace->set($sMetaCacheId, $aMeta);
        }

        // Save data with memcache.
        return $this->_cMemcace->set($sDataCacheId, $mData, 0, $nTtl);
        
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Build data cache identifier.
     *
     * @param string $sCacheId      Identifier of cache.
     * @param string $sCacheGroupId Identifier of cache group.
     *
     * @return string
     */
    private function _getDataCacheId($sCacheId, $sCacheGroupId)
    {
        return 'data_'.$sCacheId.$sCacheGroupId;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Build meta data cache identifier.
     *
     * @param string $sCacheGroupId Identifier of cache group.
     *
     * @return string
     */
    private function _getMetaCacheId($sCacheGroupId)
    {
        return 'meta_group_'.$sCacheGroupId;

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------