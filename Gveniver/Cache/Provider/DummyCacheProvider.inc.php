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
        return false;

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
        return false;

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
        return false;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Method cleans cache data by specified tags.
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