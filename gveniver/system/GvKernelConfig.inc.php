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

/**
 * Class for loader of configuration parameters.
 * 
 * TODO: Default base configuration (without xml splitter).
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class GvKernelConfig
{
    /**
     * List of configuration parameters.
     *
     * @var array
     */
    private $_aConfig;
    //-----------------------------------------------------------------------------

    /**
     * List of cached queries to configuration.
     *
     * @var array
     */
    private $_aCache;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Private singleton constructor of {@see GvKernelConfig}.
     * Load configuration parameters.
     *
     * @return void
     */
    public function __construct()
    {
        // Initialize base configuration.
        $this->_aConfig = array();

        // Try to load configuration from main configuration XML splitter.
        $this->mergeXmlFile(GV_PATH_BASE.GvConst::CONFIG_XML_FILE);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Build array of configuration parameters from XML splitter and merge with current.
     * First, trying to load configuration from cache splitter. If cache is incorrect, parse
     * XML configuration splitter and save to cache by serialization of loaded data.
     *
     * @param string $sConfigFile Path configuration XML splitter.
     * 
     * @return boolean Returns tru on success.
     */
    public function mergeXmlFile($sConfigFile)
    {
        if (!file_exists($sConfigFile))
            return false;

        // Try to load configuration from cache.
        $bCacheEnabled = GvKernel::toBoolean($this->get('Kernel/EnableCache'));
        if ($bCacheEnabled) {
            $sCacheFile = GV_PATH_CACHE.'config-'.md5($sConfigFile).'.dat';
            if (file_exists($sCacheFile) && filemtime($sCacheFile) >= filemtime($sConfigFile)) {
                $aConfig = unserialize(file_get_contents($sCacheFile));
            } else {
                $aConfig = $this->_buildXmlConfig(simplexml_load_file($sConfigFile));
                $sCacheDir = dirname($sCacheFile);
                if (!file_exists($sCacheDir))
                    mkdir($sCacheDir, 0666, true);

                file_put_contents($sCacheFile, serialize($aConfig), LOCK_EX);
            }
        } else {
            $aConfig = $this->_buildXmlConfig(simplexml_load_file($sConfigFile));
        }

        // Merge with current configuration and invalidate cache.
        $this->_merge($aConfig);
        return true;
        
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Recursive function for building configuration using SimpleXML library.
     *
     * @param SimpleXMLElement $cXml Element to parse.
     *
     * @return array
     */
    private function _buildXmlConfig(SimpleXMLElement $cXml)
    {
        $aTarget = array();
        foreach ($cXml->children() as $cParameter) {
            $sKey = $cParameter->getName();
            if (count($cParameter) > 0) {           // Complex parameter.
                $aChilds = $this->_buildXmlConfig($cParameter);
                if (count($aChilds) == 0)
                    continue;

                if (count($cXml->{$sKey}) > 1)      // List of elements with equal type.
                    $aTarget[] = $aChilds;
                else                                // List of unique.
                    $aTarget[$sKey] = $aChilds;
            } else {                                // Simple parameter.
                $sValue = trim($cParameter);
                if ($sValue)
                    $aTarget[$sKey] = $sValue;
            }
        }
        return $aTarget;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Merge configuration parameters and invalidate cache.
     *
     * @param array $aData Array with configuration parameters to merge.
     *
     * @return void
     */
    private function _merge($aData)
    {
        $this->_aConfig = array_merge_recursive_distinct($this->_aConfig, $aData);
        $this->_aCache = array();
        //echo "<pre>", print_r($this->_aConfig), "</pre>";die;
        
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

    /**
     * Set configuration parameter.
     *
     * @param string $sPath  Path of parameter in configuration tree.
     * @param mixed  $mValue Value to set.
     *
     * @return void
     */
    private function _set($sPath, $mValue)
    {
        // Remove cached value.
        unset($this->_aCache[$sPath]);

        $aKeyList = explode('/', $sPath);    // List of keys in path.
        $aResult = array();                  // Result array for merging.
        $aTarget = &$aResult;                // Target array for building path.
        $nCount = count($aKeyList);
        for ($i = 0; $i < $nCount; $i++) {
            $sKey = $aKeyList[$i];

            // Set value direct, if this is last key.
            if ($i + 1 == $nCount) {
                $aTarget[$sKey] = $mValue;
                break;

            } // End if

            // Create path and move next(key).
            $aTarget[$sKey] = array();
            $aTarget = &$aTarget[$sKey];

        } // End foreach

        // Append to configuration.
        $this->_merge($aResult);

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------