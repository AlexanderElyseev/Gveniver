<?php
/**
 * File contains class for loader of configuration parameters.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

namespace Gveniver;

/**
 * Class for loader of configuration parameters.
 * 
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class Config
{
    /**
     * List of configuration parameters.
     *
     * @var array
     */
    private $_aConfig = array();
    //-----------------------------------------------------------------------------

    /**
     * List of cached queries to configuration.
     *
     * @var array
     */
    private $_aCache = array();
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------
    
    /**
     * Build array of configuration parameters from XML file and merge with current.
     *
     * First, trying to load configuration from cache file. If cache is incorrect, parse
     * XML configuration file and save to cache by serialization of loaded data.
     *
     * !!! It is important that first load data and then read cache parameters. !!!
     *
     * @param string $sConfigFile Path configuration XML file.
     *
     * @return boolean Returns true on success.
     */
    public function mergeXmlFile($sConfigFile)
    {
        $sConfigFile = correctPath($sConfigFile);
        if (!file_exists($sConfigFile))
            return false;

        // Try to read configuration from cache.
        $bCacheEnabled = toBoolean($this->get('Kernel/EnableCache'));
        $sCacheFile = null;
        if ($bCacheEnabled) {
            $sCacheFile = GV_PATH_CACHE.'config-'.md5($sConfigFile).'.dat';
            if (file_exists($sCacheFile) && filemtime($sCacheFile) >= filemtime($sConfigFile)) {

                // Unserialize the array of configuration and check correctness.
                $aConfig = unserialize(file_get_contents($sCacheFile));
                if (is_array($aConfig)) {
                    $this->merge($aConfig);
                    return true;
                }
            }
        }

        // Load an array of configuration from XML configuration file.
        $aConfig = $this->_buildXmlConfig(simplexml_load_file($sConfigFile));
        $this->merge($aConfig);
        
        // Save cache if need.
        if ($bCacheEnabled) {
            $sCacheDir = dirname($sCacheFile);
            if (!file_exists($sCacheDir))
                mkdir($sCacheDir, 0777, true);

            file_put_contents($sCacheFile, serialize($aConfig), LOCK_EX);
        }

        return true;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Merge configuration with other configuration.
     *
     * @param Config $cConfig Configuration for merging.
     *
     * @return boolean Returns true on success.
     */
    public function mergeConfig(Config $cConfig)
    {
        $this->merge($cConfig->_aConfig);
        
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Recursive function for building configuration using SimpleXML library.
     *
     * @param \SimpleXMLElement $cXml Element to parse.
     *
     * @return array
     */
    private function _buildXmlConfig(\SimpleXMLElement $cXml)
    {
        $aTarget = array();
        foreach ($cXml->children() as $cParameter) {

            /** @var $cParameter \SimpleXMLElement */

            $sKey = $cParameter->getName();

            // Complex parameter.
            if (count($cParameter) > 0) {
                $aChilds = $this->_buildXmlConfig($cParameter);
                if (count($aChilds) == 0)
                    continue;

                // List of elements with equal type.
                if (count($cXml->{$sKey}) > 1 || $cXml['_list'])
                    $aTarget[] = $aChilds;
                else                                // List of unique.
                    $aTarget[$sKey] = $aChilds;
            } else {                                // Simple parameter.
                $sValue = trim($cParameter);
                if (mb_strlen($sValue))
                    $aTarget[$sKey] = $sValue;
            }
        }
        return $aTarget;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Merges configuration parameters and invalidates cache.
     *
     * @param array $aData Array with configuration parameters to merge.
     *
     * @return void
     */
    public function merge(array $aData)
    {
        $this->_aConfig = array_merge_recursive_distinct($this->_aConfig, $aData);
        $this->_aCache = array();
        
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Returns value of configuration by specified path.
     * If set reference variable for loading result, returns result of operation, finded
     *
     * @param string|array $mPath Path of parameter in configuration tree. Array or string.
     * @param mixed        &$mRef Reference variable for loading result.
     *
     * @return mixed Result of loading or result of loading operation, if reference result variable is set.
     */
    public function get($mPath, &$mRef = null)
    {
        // Conver path array to string, if need.
        $sPath = is_array($mPath) ?
            implode('/', $mPath) :
            (string)$mPath;

        // Try to load from cache.
        $bByRef = func_num_args() > 1;
        if (isset($this->_aCache[$sPath])) {
            // Direct return cached value.
            if (!$bByRef)
                return $this->_aCache[$sPath][1];

            // Load cached value by reference.
            if ($this->_aCache[$sPath][0])
                $mRef = $this->_aCache[$sPath][1];
            return $this->_aCache[$sPath][0];

        } // End if

        $bLoaded = null;                   // Result of loading operation.
        $aTarget = $this->_aConfig;        // Target array for befin search.
        $mResult = null;                   // Result of loading.

        $aKeyList = explode('/', $sPath);    // List of keys in path.
        $nCount = count($aKeyList);
        for ($i = 0; $i < $nCount; $i++) {
            // Not array. Prevent searching in strings.
            if (!is_array($aTarget)) {
                $bLoaded = false;
                break;

            } // End if

            // Check current key is exists.
            if (isset($aTarget[$aKeyList[$i]])) {
                if ($i + 1 == $nCount) {
                    // This is last key => finded!
                    $mResult = $aTarget[$aKeyList[$i]];
                    $bLoaded = true;
                    break;

                } else {
                    // There are some another keys in path. Search at current target.
                    $aTarget = $aTarget[$aKeyList[$i]];

                } // End else

            } // End if

        } // End for

        // Save to cache.
        $this->_aCache[$sPath] = array($bLoaded, $mResult);

        // Load by reference.
        if ($bByRef) {
            if ($bLoaded)
                $mRef = $mResult;

            return $bLoaded;

        } // End else

        // Returns result, no references.
        return $mResult;

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------