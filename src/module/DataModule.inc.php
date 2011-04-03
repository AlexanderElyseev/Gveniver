<?php
/**
 *
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

GvKernelInclude::instance()->includeFile('src/GvKernelModule.inc.php');

/**
 *
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class DataModule extends GvKernelModule
{
    /**
     * Array of {@see DataProvider} for access to data.
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
    private $_aConnections;
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

        // Empty list of providers.
        $this->_aProviders = array();

		// Load data of providers.
        $this->_aConnections = $this->cKernel->cConfig->get('Module/DataModule/Connections');

        $this->cKernel->trace->addLine('[%s] Init sucessful.', __CLASS__);
        return true;
        
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Returns connection to data source.
     *
     * @param string $sConnectionName Name of connection for load.
     *
     * @return mixed|null Returns null if connection with specified name not found or
     * on connect error.
     */
    public function getConnection($sConnectionName = null)
    {
        // Try to load provider from cache.
        $sCacheIndex = $sConnectionName ? $sConnectionName : 0;
        if (array_key_exists($sCacheIndex, $this->_aProviders)) {
            $this->cKernel->trace->addLine('[%s] Provider ("%s") loaded from cache.', __CLASS__, $sCacheIndex);
            return $this->_aProviders[$sCacheIndex];
        }

        // Try to load for existing connections.
        if ($sConnectionName) {
            $this->cKernel->trace->addLine('[%s] Load provider ("%s").', __CLASS__, $sConnectionName);

            // Create new connection by configuration name.
            foreach ($this->_aConnections as $aConnectionData) {
                // Load provider class name.
                if (!array_key_exists('ConnectionName', $aConnectionData))
                    continue;

                // Provider class name must be equal to target name.
                if ($sConnectionName != $aConnectionData['ConnectionName'])
                    continue;

                $this->_aProviders[$sConnectionName] = $this->_loadProvider($sConnectionName, $aConnectionData);
                if (!$this->_aProviders[$sConnectionName]) {
                    $this->cKernel->trace->addLine('[%s] Provider ("%s") not loaded.', __CLASS__, $sConnectionName);
                    return null;
                }
                
                return $this->_aProviders[$sConnectionName]->getConnection();

            } // End foreach

            $this->cKernel->trace->addLine('[%s] Provider ("%s") not exists.', __CLASS__, $sConnectionName);
            return null;
            
        } // End if

        // Load default (first) configuration, without name.
        $this->cKernel->trace->addLine('[%s] Load default configuration.', __CLASS__);

        // Create new default connection for first configuration in list.
        if (!count($this->_aConnections)) {
            $this->cKernel->trace->addLine('[%s] Configurations not loaded.', __CLASS__);
            return null;
        }

        // Load provider class name.
        $aConnectionData = $this->_aConnections[0];
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

        return $this->_aProviders[0]->getConnection();

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Load data provider by class name.
     *
     * @param string $sClassname Class name of data provider.
     * @param array  $aOptions   Options for provider.
     *
     * @return DataProvider|null
     */
    private function _loadProvider($sClassname, array $aOptions)
    {
        $cProvider = GvKernelInclude::createObject(
            array(
                'class' => $sClassname,
                'path'  => 'src/system/data/provider/%class%.inc.php',
                'args'  => array($this->cKernel, $aOptions)
            ),
            $nErrCode
        );
        if (!$cProvider) {
             $this->cKernel->trace->addLine(
                 '[%s] Error in create data provider ("%s"), with code: %d.',
                 __CLASS__,
                 $sClassname,
                 $nErrCode
             );
            return null;
        }

        return $cProvider;

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------