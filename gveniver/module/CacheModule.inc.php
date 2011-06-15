<?php
/**
 * File contains cache kernel module class.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

namespace Gveniver\Kernel;
\Gveniver\Loader::i('Module.inc.php');

/**
 * Cache kernel module class.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class CacheModule extends Module
{
    /**
     * Array of {@see CacheProvider} for access to cached data.
     *
     * @var array
     */
    private $_aProviders;
    //-----------------------------------------------------------------------------

    /**
     * Array of cache providers configurations.
     *
     * @var array
     */
    private $_aConfiguration;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------
    
    /**
     * Full initialization of kernel module.
     *
     * @return bool True on success.
     */
    protected function init()
    {
        $this->cKernel->trace->addLine('[%s] Init.', __CLASS__);

        // Clear list of providers.
        $this->_aProviders = array();

        // Load configuration of cache providers.
        $this->_aConfiguration = $this->cKernel->cConfig->get('Module/CacheModule/Providers');

        $this->cKernel->trace->addLine('[%s] Init sucessful.', __CLASS__);
        return true;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Returns cache provider by name.
     *
     * @param string $sProviderName Name of cache provider.
     * If it is not specified, returns default cache provider.
     *
     * @return CacheProvider
     */
    public function getProvider($sProviderName = null)
    {
        // Try to load provider from cache.
        $sCacheIndex = $sProviderName ? $sProviderName : 0;
        if (array_key_exists($sCacheIndex, $this->_aProviders)) {
            $this->cKernel->trace->addLine('[%s] Cache provider ("%s") loaded from cache.', __CLASS__, $sCacheIndex);
            return $this->_aProviders[$sCacheIndex];
        }

        // Try to load for existing provider.
        if ($sProviderName) {
            $this->cKernel->trace->addLine('[%s] Load provider ("%s").', __CLASS__, $sProviderName);

            // Create new provider by name.
            foreach ($this->_aConfiguration as $aConnectionData) {
                // Load provider class name.
                if (!array_key_exists('ProviderName', $aConnectionData))
                    continue;

                // Provider class name must be equal to target name.
                if ($sProviderName != $aConnectionData['ProviderName'])
                    continue;

                // Load provider class name.
                if (!array_key_exists('ProviderClass', $aConnectionData)) {
                    $this->cKernel->trace->addLine('[%s] Configurations not loaded.', __CLASS__);
                    return null;
                }

                $sProviderClass = $aConnectionData['ProviderClass'];
                $this->_aProviders[$sProviderName] = $this->_loadProvider($sProviderClass, $aConnectionData);
                if (!$this->_aProviders[$sProviderName]) {
                    $this->cKernel->trace->addLine('[%s] Provider ("%s") not loaded.', __CLASS__, $sProviderName);
                    return null;
                }

                return $this->_aProviders[$sProviderName];

            } // End foreach

            $this->cKernel->trace->addLine('[%s] Provider ("%s") not exists.', __CLASS__, $sProviderName);
            return null;

        } // End if

        // Load default (first) configuration, without name.
        $this->cKernel->trace->addLine('[%s] Load default configuration.', __CLASS__);

        // Create new default connection for first configuration in list.
        if (!count($this->_aConfiguration)) {
            $this->cKernel->trace->addLine('[%s] Configurations not loaded.', __CLASS__);
            return null;
        }

        // Load provider class name.
        $aConnectionData = $this->_aConfiguration[0];
        if (!array_key_exists('ProviderClass', $aConnectionData)) {
            $this->cKernel->trace->addLine('[%s] Configurations not loaded.', __CLASS__);
            return null;
        }

        $this->cKernel->trace->addLine('[%s] Create provider from first configuration.', __CLASS__);

        $sProviderClass = $aConnectionData['ProviderClass'];
        $this->_aProviders[0] = $this->_loadProvider($sProviderClass, $aConnectionData);
        if (!$this->_aProviders[0]) {
            $this->cKernel->trace->addLine('[%s] Default configuration not loaded.', __CLASS__);
            return null;
        }

        return $this->_aProviders[0];
        
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Load cache provider by class name.
     *
     * @param string $sClassname Class name of cache provider.
     * @param array  $aOptions   Options for provider.
     *
     * @return CacheProvider|null
     */
    private function _loadProvider($sClassname, array $aOptions)
    {
        $cProvider = \Gveniver\Loader::createObject(
            array(
                'class' => $sClassname,
                'path'  => 'system/cache/provider/%class%.inc.php',
                'args'  => array($this->cKernel, $aOptions)
            ),
            $nErrCode
        );
        if (!$cProvider) {
             $this->cKernel->trace->addLine(
                 '[%s] Error in create cache provider ("%s"), with code: %d.',
                 __CLASS__,
                 $sClassname,
                 $nErrCode
             );
            return null;
        }

        return $cProvider;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Load data form cache.
     *
     * @param string $sCacheId      Identifier of cache.
     * @param string $sCacheGroupId Identifier of cache group.
     * @param mixed  &$cRef         Reference variable for loading cached data.
     * If specified, then the data loads to variable by refernce and returns
     * result of operation (boolean). Otherwise, returns data.
     *
     * @return mixed|boolean
     */
    public function get($sCacheId, $sCacheGroupId = 'Default', &$cRef = null)
    {
        // Load default cache provider.
        $cProvider = $this->getProvider();
        if (!$cProvider)
            throw new \Gveniver\Exception\Exception('Default cache provider not loaded.');

        // Load data from cache by default provider.
        $mData = null;
        $bResult = $cProvider->get($sCacheId, $sCacheGroupId, $mData);

        // Return result by loading type.
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
     * Save data to cache.
     *
     * @param mixed  $mData         Data to save.
     * @param strin  $sCacheId      Identifier of cache.
     * @param string $sCacheGroupId Identifier of cache group.
     * @param int    $nTtl          Time to live for cache.
     *
     * @return boolean True on success.
     */
    public function set($mData, $sCacheId, $sCacheGroupId = 'Default', $nTtl = 1200)
    {
        // Load default cache provider.
        $cProvider = $this->getProvider();
        if (!$cProvider)
            throw new \Gveniver\Exception\Exception('Default cache provider not loaded.');

        // Save data to cache by default provider.
        return $cProvider->set($mData, $sCacheId, $sCacheGroupId, $nTtl);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Flush cache data.
     *
     * @param string $sCacheGroupId Identifier of cache group.
     *
     * @return boolean True on success.
     */
    public function flush($sCacheGroupId = 'Default')
    {
        // Load default cache provider.
        $cProvider = $this->getProvider();
        if (!$cProvider)
            throw new \Gveniver\Exception\Exception('Default cache provider not loaded.');

        // Flush cache data by default provider.
        return $cProvider->flush($sCacheGroupId);

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------