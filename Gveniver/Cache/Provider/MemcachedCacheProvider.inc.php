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
     * @var \Memcache|\Memcached
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
        if (class_exists('\\Memcached'))
            $this->_cMemcache = new \Memcached();
        elseif (class_exists('\\Memcache'))
            $this->_cMemcache = new \Memcache();
        else
            throw new \Gveniver\Exception\BaseException('Memcache(d) PHP extension is not loaded.');

        // Adding  servers.
        if (!isset($aOptions['Servers']) || !is_array($aOptions['Servers'])) {
            $this->getApplication()->trace->addLine('[%s] Using local memcached server.', __CLASS__);
            $this->_cMemcache->addServer('localhost');
        } else {
            foreach ($aOptions['Servers'] as $aServerData) {
                $sServerHost = isset($aServerData['Host']) ? $aServerData['Host'] : null;
                if (!$sServerHost)
                    continue;

                if (isset($aServerData['Port']) && $aServerData['Port'])
                    $this->_cMemcache->addServer($sServerHost, $aServerData['Port']);
                else
                    $this->_cMemcache->addServer($sServerHost);

            } // End foreach

        } // End else

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Class destructor.
     *
     * Closes connection with memcached.
     */
    public function __destruct()
    {
        if ($this->_cMemcache)
            $this->_cMemcache->close();

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
        return $this->_cMemcache->delete($this->_getDataCacheId($sCacheId));

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
     * Method cleans cached data by specified tags.
     *
     * @param array $aTags List of tags for cleaning.
     *
     * @return boolean True on success.
     */
    public function cleanByTags(array $aTags)
    {
       foreach ($aTags as $sTag)
           $this->_invalidateTag($this->_getTagCacheId($sTag));

        return true;

    } // End function
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
        // Loading cached data and checking structure.
        $aCacheData = $this->_cMemcache->get($this->_getDataCacheId($sCacheId));
        if (!is_array($aCacheData)
            || !array_key_exists('value', $aCacheData)
            || !array_key_exists('tags', $aCacheData)
            || !array_key_exists('ttl', $aCacheData)
        )
            return false;

        // Checking if cached data is alive.
        if ($aCacheData['ttl'] && GV_TIME_NOW > $aCacheData['ttl'])
            return false;

        // Checking if all tags of cached data is alive.
        foreach ($aCacheData['tags'] as $sTag => $fVersion)
            if ($this->_getTagVersion($this->_getTagCacheId($sTag)) > $fVersion)
                return false;

        $cRef = $aCacheData['value'];
        return true;

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
        // Building data of tags.
        $aTagData = array();
        foreach ($aTags as $sTag)
            $aTagData[$sTag] = $this->_getTagVersion($this->_getTagCacheId($sTag));

        // Building data for caching.
        $aCacheData = array(
            'value' => $mData,
            'tags'  => $aTagData,
            'ttl'   => $nTtl ? time() + $nTtl : null
        );

        return $this->_cMemcache->set($this->_getDataCacheId($sCacheId), $aCacheData, 0, $nTtl);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Build data cache identifier.
     *
     * @param string $sCacheId The identifier of cached data.
     *
     * @return string
     */
    private function _getDataCacheId($sCacheId)
    {
        return 'data_'.md5($sCacheId);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Method builds identifier of record with metadata of specified cache tag.
     *
     * @param string $sTag Name of tag.
     *
     * @return string
     */
    private function _getTagCacheId($sTag)
    {
        return 'tag_'.md5($sTag);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Returns version of specified tag. If tag is not exists in cache, it will be created.
     *
     * @param string $sTagCacheId Identifier of cache for tag metadata.
     *
     * @return float
     */
    private function _getTagVersion($sTagCacheId)
    {
        $mVersion = $this->_cMemcache->get($sTagCacheId);
        if (!$mVersion)
            return $this->_invalidateTag($sTagCacheId);

        return floatval($mVersion);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Increase version of tag. This operation automatically invalidates all data that are related to the tag.
     *
     * @param string $sTagCacheId Identifier of cache for tag metadata.
     *
     * @return float New version of tag.
     */
    private function _invalidateTag($sTagCacheId)
    {
        $fVersion = round(microtime(true), 3);
        $this->_cMemcache->set($sTagCacheId, $fVersion);
        return $fVersion;

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------