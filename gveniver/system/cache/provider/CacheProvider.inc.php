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

namespace Gveniver\Cache;

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
abstract class CacheProvider
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
     * @param string $sCacheId      Identifier of cache.
     * @param string $sCacheGroupId Identifier of cache group.
     * @param mixed  &$cRef         Reference variable for loading cached data.
     *
     * @return boolean True on success loading
     * @abstract
     */
    public abstract function get($sCacheId, $sCacheGroupId, &$cRef);
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
     * @abstract
     */
    public abstract function set($mData, $sCacheId, $sCacheGroupId, $nTtl);
    //-----------------------------------------------------------------------------

    /**
     * Flush cache data.
     *
     * @param string $sCacheGroupId Identifier of cache group.
     *
     * @return boolean True on success.
     * @abstract
     */
    public abstract function flush($sCacheGroupId);
    //-----------------------------------------------------------------------------
    
    /**
     * Generator for correct unique cache identifiers by names.
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