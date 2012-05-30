<?php
/**
 * File contains base abstract cache provider class.
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
 * Base abstract cache provider class.
 *
 * @category  Gveniver
 * @package   Cache
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 * @abstract
 */
abstract class BaseCacheProvider extends \Gveniver\BaseObject
{
    /**
     * Base constructor.
     * Initialize member fields.
     *
     * @param \Gveniver\Kernel\Application $cApplication Current application.
     * @param array                        $aOptions     Options for cache provider.
     */
    public function __construct(\Gveniver\Kernel\Application $cApplication, array $aOptions)
    {
        parent::__construct($cApplication);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Method loads cached data by identifier.
     *
     * @param string $sCacheId The identifier of cached data.
     * @param mixed  &$cRef    Reference variable for loading cached data.
     *
     * @return boolean True on success loading.
     * @abstract
     */
    public abstract function get($sCacheId, &$cRef);
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
     * @abstract
     */
    public abstract function set($mData, $sCacheId, array $aTags, $nTtl);
    //-----------------------------------------------------------------------------

    /**
     * Method cleans cached data by identifier.
     *
     * @param string $sCacheId Identifier of cache for cleaning.
     *
     * @return boolean True on success.
     * @abstract
     */
    public abstract function clean($sCacheId);
    //-----------------------------------------------------------------------------

    /**
     * Method cleans cached data by specified tags.
     *
     * @param array $aTags List of tags for cleaning.
     *
     * @return boolean True on success.
     * @abstract
     */
    public abstract function cleanByTags(array $aTags);
    //-----------------------------------------------------------------------------

    /**
     * Method cleans all cached data.
     *
     * @return boolean True on success.
     * @abstract
     */
    public abstract function cleanAll();
    //-----------------------------------------------------------------------------

    /**
     * Generated correct unique cache identifiers by name.
     *
     * @param string $sDataName Unique name of cached data.
     * 
     * @return string
     */
    public function generateId($sDataName)
    {
        return md5((string)$sDataName);

    } // End function
    //-----------------------------------------------------------------------------
    
} // End class
//-----------------------------------------------------------------------------