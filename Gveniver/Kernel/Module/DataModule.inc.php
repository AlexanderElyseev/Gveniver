<?php
/**
 * File contains data module class.
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
 * Data module class.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class DataModule extends BaseModule
{
    /**
     * Array of {@see \Gveniver\Data\Provider\BaseDataProvider} for access to data.
     *
     * @var array
     */
    private $_aProviders;
    //-----------------------------------------------------------------------------

    /**
     * Array of data for connections.
     * 
     * @var array
     */
    private $_aConfiguration;
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

        // Clear list of providers.
        $this->_aProviders = array();

        // Load data of providers.
        $this->_aConfiguration = $this->getApplication()->getConfig()->get('Module/DataModule/Providers');

        $this->getApplication()->trace->addLine('[%s] Init successful.', __CLASS__);
        return true;
        
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Returns connection to data source.
     *
     * @param string $sProviderName Name of provider for load.
     *
     * @return mixed|null Returns null if connection with specified name not found or
     * on connect error.
     */
    public function getConnection($sProviderName = null)
    {
        // Try to load provider from cache.
        $sCacheIndex = $sProviderName ? $sProviderName : 0;
        if (array_key_exists($sCacheIndex, $this->_aProviders)) {
            $this->getApplication()->trace->addLine('[%s] Provider ("%s") loaded from cache.', __CLASS__, $sCacheIndex);
            return $this->_aProviders[$sCacheIndex]->getConnection();
        }

        // Try to load for existing provider.
        if ($sProviderName) {
            $this->getApplication()->trace->addLine('[%s] Load provider ("%s").', __CLASS__, $sProviderName);

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
                    $this->getApplication()->trace->addLine('[%s] Configurations not loaded.', __CLASS__);
                    return null;
                }

                $sProviderClass = $aConnectionData['ProviderClass'];
                $this->_aProviders[$sProviderName] = $this->_loadProvider($sProviderClass, $aConnectionData);
                if (!$this->_aProviders[$sProviderName]) {
                    $this->getApplication()->trace->addLine('[%s] Provider ("%s") not loaded.', __CLASS__, $sProviderName);
                    return null;
                }
                
                return $this->_aProviders[$sProviderName]->getConnection();

            } // End foreach

            $this->getApplication()->trace->addLine('[%s] Provider ("%s") not exists.', __CLASS__, $sProviderName);
            return null;
            
        } // End if

        // Load default (first) configuration, without name.
        $this->getApplication()->trace->addLine('[%s] Load default configuration.', __CLASS__);

        // Create new default connection for first configuration in list.
        if (!count($this->_aConfiguration)) {
            $this->getApplication()->trace->addLine('[%s] Configurations not loaded.', __CLASS__);
            return null;
        }

        // Load provider class name.
        $aConnectionData = $this->_aConfiguration[0];
        if (!array_key_exists('ProviderClass', $aConnectionData)) {
            $this->getApplication()->trace->addLine('[%s] Configurations not loaded.', __CLASS__);
            return null;
        }

        $this->getApplication()->trace->addLine('[%s] Create provider from first configuration.', __CLASS__);

        $sProviderClass = $aConnectionData['ProviderClass'];
        $this->_aProviders[0] = $this->_loadProvider($sProviderClass, $aConnectionData);
        if (!$this->_aProviders[0]) {
            $this->getApplication()->trace->addLine('[%s] Default configuration not loaded.', __CLASS__);
            return null;
        }

        return $this->_aProviders[0]->getConnection();

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Load data provider by class name.
     *
     * @param string $sClassName Class name of data provider.
     * @param array  $aOptions   Options for provider.
     *
     * @return DataProvider|null
     */
    private function _loadProvider($sClassName, array $aOptions)
    {
        $sClassName = '\\Gveniver\\Data\\Provider\\'.$sClassName;
        $cProvider = new $sClassName($this->getApplication(), $aOptions);
        if (!$cProvider) {
             $this->getApplication()->trace->addLine(
                 '[%s] Error in create data provider ("%s").',
                 __CLASS__,
                 $sClassName
             );
            return null;
        }
        
        return $cProvider;

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------