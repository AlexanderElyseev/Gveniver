<?php
/**
 * File contains cache module class.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

namespace Gveniver\Kernel\Module;

/**
 * Cache module class.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class CacheModule extends BaseModule
{
    /**
     * Default TTL for cache.
     *
     * @var int
     */
    const DEFAULT_TTL = 1200;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Configuration parameters of module.
     *
     * @var array
     */
    private $_aModuleConfiguration;
    //-----------------------------------------------------------------------------

    /**
     * Configuration parameters of providers.
     *
     * @var array
     */
    private $_aProvidersConfiguration = array();
    //-----------------------------------------------------------------------------

    /**
     * Name of default provider loaded from configuration.
     *
     * @var string
     */
    private $_sDefaultProviderName;
    //-----------------------------------------------------------------------------

    /**
     * Array of {@see BaseCacheProvider} for fast access to cached providers
     * using provider name as key.
     *
     * @var array
     */
    private $_aProvidersCache = array();
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Full initialization of module.
     *
     * @return bool True on success.
     */
    protected function init()
    {
        $this->getApplication()->trace->addLine('[%s] Init.', __CLASS__);

        // Load configuration of cache providers.
        $this->_aModuleConfiguration = $this->getApplication()->getConfig()->get('Module/CacheModule');
        if (!is_array($this->_aModuleConfiguration))
            $this->getApplication()->trace->addLine('[%s] Configuration of cache module is not loaded.', __CLASS__);
        else
            $this->getApplication()->trace->addLine('[%s] Configuration of cache module is successfully loaded.', __CLASS__);

        if (!isset($this->_aModuleConfiguration['Providers']))
            $this->getApplication()->trace->addLine('[%s] Configuration of providers is not loaded.', __CLASS__);
        else {
            $this->_aProvidersConfiguration = $this->_aModuleConfiguration['Providers'];
            $this->getApplication()->trace->addLine('[%s] Configuration of providers is successfully loaded.', __CLASS__);
        }

        if (isset($this->_aModuleConfiguration['DefaultProvider']) && $this->_aModuleConfiguration['DefaultProvider'] && is_string($this->_aModuleConfiguration['DefaultProvider'])) {
            $this->_sDefaultProviderName = $this->_aModuleConfiguration['DefaultProvider'];
            $this->getApplication()->trace->addLine('[%s] Default provider name ("%s") from configuration is successfully loaded.', __CLASS__, $this->_sDefaultProviderName);
        }

        $this->getApplication()->trace->addLine('[%s] Init successful.', __CLASS__);
        return true;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Generator for correct unique cache identifiers by names.
     *
     * @param string $sDataName Unique name of cached data.
     *
     * @throws \Gveniver\Exception\BaseException
     * @return string
     */
    public function generateId($sDataName)
    {
        $cProvider = $this->getProvider();
        if (!$cProvider)
            throw new \Gveniver\Exception\BaseException('Default cache provider is not loaded.');

        return $cProvider->generateId($sDataName);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Returns cache provider by name.
     *
     * @param string $sProviderName Name of cache provider.
     *                              If it is not specified, returns default cache provider.
     *
     * @return \Gveniver\Cache\Provider\BaseCacheProvider
     */
    public function getProvider($sProviderName = null)
    {
        // Loading provider by name.
        if ($sProviderName) {
            $this->getApplication()->trace->addLine('[%s] Loading cache provider by specified name ("%s").', __CLASS__, $sProviderName);
            $sCacheId = 'name_'.$sProviderName;
            if (array_key_exists($sCacheId, $this->_aProvidersCache)) {
                $this->getApplication()->trace->addLine('[%s] Cache provider ("%s") is loaded from cache.', __CLASS__, $sProviderName);
                return $this->_aProvidersCache[$sCacheId];
            }
            return $this->_aProvidersCache[$sCacheId] = $this->_loadProviderByConfiguration($this->_loadProviderConfiguration($sProviderName));
        }

        // Loading default provider without name.
        $sCacheId = 'default';
        if (array_key_exists($sCacheId, $this->_aProvidersCache)) {
            $this->getApplication()->trace->addLine('[%s] Default cache provider is loaded from cache.', __CLASS__);
            return $this->_aProvidersCache[$sCacheId];
        }

        if ($this->_sDefaultProviderName) {
            $this->getApplication()->trace->addLine('[%s] Loading default cache provider by default name ("%s").', __CLASS__, $this->_sDefaultProviderName);
            return $this->_aProvidersCache[$sCacheId] = $this->_loadProviderByConfiguration($this->_loadProviderConfiguration($this->_sDefaultProviderName));
        } elseif (count($this->_aProvidersConfiguration) > 0) {
            $this->getApplication()->trace->addLine('[%s] Loading default cache provider from the first configuration in the list.', __CLASS__);
            return $this->_aProvidersCache[$sCacheId] = $this->_loadProviderByConfiguration(reset($this->_aProvidersConfiguration));
        } else {
            $this->getApplication()->trace->addLine('[%s] Loading dummy cache provider as default cache provider.', __CLASS__);
            return $this->_aProvidersCache[$sCacheId] = $this->_loadDummyProvider();
        }

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Method loads provider with specified configuration data.
     *
     * @param array $aConnectionData Configuration parameters of provider for creation.
     *
     * @return \Gveniver\Cache\Provider\BaseCacheProvider|null Returns null on error.
     */
    private function _loadProviderByConfiguration(array $aConnectionData)
    {
        if (array_key_exists('ProviderClass', $aConnectionData)) {
            $sProviderClass = $aConnectionData['ProviderClass'];
            $aProviderArgs = array_key_exists('Args', $aConnectionData) ? $aConnectionData['Args'] : array();
            return $this->_loadProviderInstance($sProviderClass, $aProviderArgs);
        }

        $this->getApplication()->trace->addLine('[%s] Configuration of provider is not loaded or incorrect.', __CLASS__);
        return null;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Method loads dummy cache provider.
     *
     * @return \Gveniver\Cache\Provider\BaseCacheProvider|null Returns null on error.
     */
    private function _loadDummyProvider()
    {
        $cProvider = $this->_loadProviderInstance('DummyCacheProvider', array());
        if ($cProvider) {
            $this->getApplication()->trace->addLine('[%s] Dummy cache provider is successfully loaded.', __CLASS__);
            return $cProvider;
        }

        $this->getApplication()->trace->addLine('[%s] Dummy cache provider is not loaded.', __CLASS__);
        return null;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Method loads configuration of provider with specified name.
     *
     * @param string $sProviderName Name of provider for loading configuration.
     *
     * @return array Returns array with configuration of provider or empty array if it is not found.
     */
    private function _loadProviderConfiguration($sProviderName)
    {
        foreach ($this->_aProvidersConfiguration as $aConnectionData) {
            if (!array_key_exists('ProviderName', $aConnectionData))
                continue;

            if ($sProviderName != $aConnectionData['ProviderName'])
                continue;

            return $aConnectionData;
        }

        $this->getApplication()->trace->addLine('[%s] Configuration of provider ("%s") is not exists.', __CLASS__, $sProviderName);
        return array();

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Load cache provider by class name.
     *
     * @param string $sClassName Class name of cache provider.
     * @param array  $aOptions   Options for provider.
     *
     * @return \Gveniver\Cache\Provider\BaseCacheProvider|null
     */
    private function _loadProviderInstance($sClassName, array $aOptions)
    {
        $sClassName = '\\Gveniver\Cache\\Provider\\'.$sClassName;
        if (!class_exists($sClassName)) {
            $this->getApplication()->trace->addLine('[%s] Cache provider class ("%s") is not exist.', __CLASS__, $sClassName);
            return null;
        }

        try {
            $cProvider = new $sClassName($this->getApplication(), $aOptions);
            if (!$cProvider) {
                $this->getApplication()->trace->addLine('[%s] Error in creating cache provider ("%s").', __CLASS__, $sClassName);
                return null;
            }
            return $cProvider;

        } catch (\Exception $cEx) {
            $this->getApplication()->trace->addLine('[%s] Exception in create cache provider ("%s"): "%s".', __CLASS__, $sClassName, $cEx->getMessage());
            return null;
        }

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Load data form cache.
     *
     * @param string $sCacheId Identifier of cache.
     * @param mixed  &$cRef    Reference variable for loading cached data.
     *                         If specified, then the data loads to variable by refernce and methods returns
     *                         result of operation (boolean). Otherwise, method returns cached data.
     *
     * @throws \Gveniver\Exception\BaseException
     *
     * @return mixed|boolean
     */
    public function get($sCacheId, &$cRef = null)
    {
        $cProvider = $this->getProvider();
        if (!$cProvider)
            throw new \Gveniver\Exception\BaseException('Default cache provider has not been loaded.');

        $mData = null;
        $bResult = $cProvider->get($sCacheId, $mData);

        $bByRef = func_num_args() == 3;
        if ($bByRef) {
            if ($bResult)
                $cRef = $mData;

            return $bResult;
        }

        return $mData;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Method saves data to the cache.
     *
     * @param mixed        $mData    Data to save.
     * @param string       $sCacheId Identifier of cache.
     * @param array|string $mTags    List of tags for this cache.
     * @param int          $nTtl     Time to live for cache.
     *
     * @throws \Gveniver\Exception\BaseException     Throws if cache provider is not found.
     * @throws \Gveniver\Exception\ArgumentException Throws if tag parameter has wrong type.
     * @return boolean True on success.
     */
    public function set($mData, $sCacheId, $mTags, $nTtl = self::DEFAULT_TTL)
    {
        $cProvider = $this->getProvider();
        if (!$cProvider)
            throw new \Gveniver\Exception\BaseException('Default cache provider has not been loaded.');

        if (is_string($mTags))
            $mTags = array($mTags);
        elseif (!is_array($mTags))
            throw new \Gveniver\Exception\ArgumentException('Tags can be string or array of strings only.');

        return $cProvider->set($mData, $sCacheId, $mTags, $nTtl);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Method cleans cache data by specified parameters.
     *
     * @param string $sCacheId Identifier of cache for cleaning.
     *
     * @throws \Gveniver\Exception\BaseException Throws if cache provider is not found.
     * @return boolean True on success.
     */
    public function clean($sCacheId)
    {
        $cProvider = $this->getProvider();
        if (!$cProvider)
            throw new \Gveniver\Exception\BaseException('Default cache provider has not been loaded.');

        return $cProvider->clean($sCacheId);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Method cleans cached data by specified tags.
     *
     * @param array|string $mTags List of tags for cleaning cache.
     *
     * @throws \Gveniver\Exception\BaseException     Throws if cache provider is not found.
     * @throws \Gveniver\Exception\ArgumentException Throws if tag parameter has wrong type.
     * @return boolean True on success.
     */
    public function cleanByTags($mTags)
    {
        $cProvider = $this->getProvider();
        if (!$cProvider)
            throw new \Gveniver\Exception\BaseException('Default cache provider has not been loaded.');

        if (is_string($mTags))
            $mTags = array($mTags);
        elseif (!is_array($mTags))
            throw new \Gveniver\Exception\ArgumentException('Tags can be string or array of strings only.');

        return $cProvider->cleanByTags($mTags);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Method cleans all cache data.
     *
     * @throws \Gveniver\Exception\BaseException Throws if cache provider is not found.
     * @return boolean True on success.
     */
    public function cleanAll()
    {
        $cProvider = $this->getProvider();
        if (!$cProvider)
            throw new \Gveniver\Exception\BaseException('Default cache provider has not been loaded.');

        return $cProvider->cleanAll();

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------