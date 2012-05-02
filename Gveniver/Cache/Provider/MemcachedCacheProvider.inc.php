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
class MemcachedCacheProvider extends BaseCacheProvider
{
    /**
     * Memcached instance.
     * 
     * @var \Memcached
     */
    private $_cMemcache;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------
    
    /**
     * Class constructor.
     *
     * @param \Gveniver\Kernel\Application $cApplication Current application.
     * @param array                        $aOptions     Options for cache provider.
     *
     * @throws \Gveniver\Exception\BaseException
     */
    public function __construct(\Gveniver\Kernel\Application $cApplication, array $aOptions)
    {
        parent::__construct($cApplication, $aOptions);

        // Checking for Mecached PHP extension.
        if (!class_exists('\\Memcache'))
            throw new \Gveniver\Exception\BaseException('Memcache PHP extension is not loaded.');

        $this->_cMemcache = new \Memcache();

        // Adding  servers.
        if (!isset($this->aOptions['Servers']))
            throw new \Gveniver\Exception\BaseException('Memcache servers are not loaded.');

        foreach ($this->aOptions['Servers'] as $aServerData) {
            $sServerHost = isset($aServerData['Host']) ? $aServerData['Host'] : null;
            if (!$sServerHost)
                continue;

            $this->_cMemcache->addServer($sServerHost);
        }

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Method cleans cache data by specified parameters.
     *
     * @param string $sNamespace Namespace of cache.
     * @param string $sCacheId   Identifier of cache. If it is specified, clean only record with specified identifier.
     * Otherwise, clean all namespace.
     *
     * @return boolean True on success.
     */
    public function clean($sNamespace, $sCacheId = null)
    {
        if ($sCacheId)
            return $this->_cMemcache->delete($this->_getDataCacheId($sCacheId, $sNamespace));

        $aMeta = $this->_cMemcache->get($this->_getNamespaceMetaCacheId($sNamespace));
        if (!is_array($aMeta))
            return true;

        $bResult = true;
        foreach ($aMeta as $sDataCacheId)
            $bResult = $bResult && $this->_cMemcache->delete($sDataCacheId);

        return $bResult;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Method cleans all cache data.
     *
     * @return boolean True on success.
     */
    public function cleanAll()
    {
        return $this->_cMemcache->flush();

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
        $bResult = true;
        foreach ($aTags as $sTag) {
            $aMeta = $this->_cMemcache->get($this->_getTagMetaCacheId($sTag));
            if (!is_array($aMeta))
                continue;

            foreach ($aMeta as $sDataCacheId)
                $bResult = $bResult && $this->_cMemcache->delete($sDataCacheId);
        }

        return $bResult;

    } // End function
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
        $mData = $this->_cMemcache->get($this->_getDataCacheId($sCacheId, $sNamespace));
        $bResult = $mData !== false;
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
        $sDataCacheId = $this->_getDataCacheId($sCacheId, $sNamespace);

        // Update group content.
        $sMetaCacheId = $this->_getNamespaceMetaCacheId($sNamespace);
        $aMeta = $this->_cMemcache->get($sMetaCacheId);
        if (!is_array($aMeta)) {
            $aMeta = array($sDataCacheId);
            $this->_cMemcache->set($sMetaCacheId, $aMeta);
        } elseif (!in_array($sDataCacheId, $aMeta)) {
            $aMeta[] = $sDataCacheId;
            $this->_cMemcache->set($sMetaCacheId, $aMeta);
        }
        
        // Update tag content.
        foreach ($aTags as $sTag) {
            $sTagCacheId = $this->_getTagMetaCacheId($sTag);
            $aTag = $this->_cMemcache->get($sTagCacheId);
            if (!is_array($aTag)) {
                $aTag = array($sDataCacheId);
                $this->_cMemcache->set($sTagCacheId, $aTag);
            } elseif (!in_array($sDataCacheId, $aTag)) {
                $aTag[] = $sDataCacheId;
                $this->_cMemcache->set($sTagCacheId, $aTag);
            }
        }
        
        // Save data with memcache.
        return $this->_cMemcache->set($sDataCacheId, $mData, 0, $nTtl);
        
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Build data cache identifier.
     *
     * @param string $sCacheId   Identifier of cache.
     * @param string $sNamespace Namespace of cache.
     *
     * @return string
     */
    private function _getDataCacheId($sCacheId, $sNamespace)
    {
        return 'data_'.md5($sCacheId).'_'.md5($sNamespace);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Method builds identifier of record with metadata of specified cache namespace.
     *
     * @param string $sNamespace Identifier of cache group.
     *
     * @return string
     */
    private function _getNamespaceMetaCacheId($sNamespace)
    {
        return 'ns_'.md5($sNamespace);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Method builds identifier of record with metadata of specified cache tag.
     *
     * @param string $sTag Identifier of cache group.
     *
     * @return string
     */
    private function _getTagMetaCacheId($sTag)
    {
        return 'tag_'.md5($sTag);

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------