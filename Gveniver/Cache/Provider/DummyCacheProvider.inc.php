<?php
/**
 * File contains dummy cache provider class.
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
 * Dummy cache provider class.
 *
 * @category  Gveniver
 * @package   Cache
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class DummyCacheProvider extends BaseCacheProvider
{
    /**
     * Method loads data form cache.
     *
     * @param string $sCacheId The identifier of cached data.
     * @param mixed  &$cRef    Reference variable for loading cached data.
     *
     * @return boolean True on success loading.
     */
    public function get($sCacheId, &$cRef)
    {
        return false;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Method saves data to cache.
     *
     * @param mixed  $mData    Data for caching.
     * @param string $sCacheId The identifier of cached data.
     * @param array  $aTags    List of tags for this cache record.
     * @param int    $nTtl     Time to live for cache.
     *
     * @return boolean True on success.
     */
    public function set($mData, $sCacheId, array $aTags, $nTtl)
    {
        return false;

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
        return false;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Method cleans all cached data.
     *
     * @return boolean True on success.
     */
    public function cleanAll()
    {
        return false;

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
        return false;

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------