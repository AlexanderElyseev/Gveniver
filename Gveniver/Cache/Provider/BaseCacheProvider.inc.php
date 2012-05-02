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
abstract class BaseCacheProvider
{
    /**
     * Current application.
     *
     * @var \Gveniver\Kernel\Application
     */
    private $_cApplication;
    //-----------------------------------------------------------------------------

    /**
     * Array of options for cache provider.
     *
     * @var array
     */
    protected $aOptions;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Base constructor.
     * Initialize member fields.
     *
     * @param \Gveniver\Kernel\Application $cApplication Current application.
     * @param array                        $aOptions     Options for cache provider.
     */
    public function __construct(\Gveniver\Kernel\Application $cApplication, array $aOptions)
    {
        $this->_cApplication = $cApplication;
        $this->aOptions = $aOptions;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Getter for current application.
     *
     * @return \Gveniver\Kernel\Application
     */
    public function getApplication()
    {
        return $this->_cApplication;

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
     * @abstract
     */
    public abstract function get($sCacheId, $sNamespace, &$cRef);
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
     * @abstract
     */
    public abstract function set($mData, $sCacheId, $sNamespace, array $aTags, $nTtl);
    //-----------------------------------------------------------------------------

    /**
     * Method cleans cache data by specified parameters.
     *
     * @param string $sNamespace Namespace of cache.
     * @param string $sCacheId   Identifier of cache. If it is specified, clean only record with specified identifier.
     * Otherwise, clean all namespace.
     *
     * @return boolean True on success.
     * @abstract
     */
    public abstract function clean($sNamespace, $sCacheId = null);
    //-----------------------------------------------------------------------------

    /**
     * Method cleans cache data by specified tags.
     *
     * @param array $aTags List of tags for cleaning.
     *
     * @return boolean True on success.
     * @abstract
     */
    public abstract function cleanByTags(array $aTags);
    //-----------------------------------------------------------------------------

    /**
     * Method cleans all cache data.
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