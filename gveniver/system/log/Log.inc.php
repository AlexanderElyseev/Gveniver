<?php
/**
 * File contains base logger class.
 * 
 * @category  Gveniver
 * @package   Log
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

GvInclude::instance()->includeFile('gveniver/system/log/provider/LogProvider.inc.php');

/**
 * Base logger class.
 * Support this error levels:
 *  - Fatal error;
 *  - Error;
 *  - Security;
 *  - Warning;
 *  - Information,
 * and its any bitwise combination.
 * 
 * Log level may be assigned for logger and for each save provider. Providers will save
 * records, that corresponds to provider log level (if specified) or logger level by
 * default.
 *
 * @category  Gveniver
 * @package   Log
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class Log
{
    /**
     * List of saving providers for log.
     *
     * @var array
     */
    private $_aProviders;
    //-----------------------------------------------------------------------------

    /**
     * Target log level.
     *
     * @var int
     */
    private $_nCommonLogLevel;
    //-----------------------------------------------------------------------------
    
    /**
     * List of log records.
     *
     * @var array
     */
    private $_aCommonContainer;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------
    
    /**
     * Class constructor.
     * 
     * @param int $nLevel Target log level.
     *
     * @return void
     */
    public function __construct($nLevel)
    {
        $this->_nCommonLogLevel = intval($nLevel);
        $this->_aCommonContainer = array();
        $this->_aProviders = array();

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Overloaded call method for various log level functions.
     *
     * @param string $name      Name of log level.
     * @param array  $arguments Arguments of functions. Message, code and additional data.
     *
     * @return void
     */
    public function __call($name, array $arguments)
    {
        $nLevel = LogProvider::getLevelByName($name);

        // Build log record data.
        $sMessage = isset($arguments[0]) ? (string)$arguments[0] : null;
        $nCode = isset($arguments[1]) ? (int)$arguments[1] : null;
        $aData = isset($arguments[2]) ? $arguments[2] : null;
        $aRecord = array(
            'time' => time(),
            'level' => $nLevel,
            'message' => $sMessage,
            'code' => $nCode,
            'data' => $aData
        );
        
        // Append record to provider with corresponded level and common list.
        if ($this->_nCommonLogLevel & $aRecord['level'])
            $this->_aCommonContainer[] = $aRecord;
        foreach ($this->_aProviders as &$aProvider)
            if ($aProvider['level'] & $aRecord['level'])
                $aProvider['records'][] = $aRecord;
        
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Save log with specified provider or with all providers in list.
     * 
     * @param LogSaveProvider $cProvider Target provider to save (if need).
     * @param int             $nLevel    Log level for specified provider (if need).
     *
     * @return void
     */
    public function save(LogProvider $cProvider = null, $nLevel = null)
    {
        if (count($this->_aCommonContainer) == 0)
            return;
        
        if ($cProvider) {
            // Save data with specified provider with specified target log level.
            // No need to clear common container, for future added providers.
            $cProvider->Save(is_null($nLevel) ? $this->_aCommonContainer : $this->_getFilterdRecords($nLevel));
        } else {
            // Save records of all providers and clear list.
            foreach ($this->_aProviders as &$aProvider) {
                $aProvider['provider']->Save($aProvider['records']);
                $aProvider['records'] = array();
            }
        }
        
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Filter common list of log records by specified log level.
     * 
     * @param int $nTargetLevel Log level for filtering.
     *
     * @return array
     */
    private function _getFilterdRecords($nTargetLevel)
    {
        if ($this->_nCommonLogLevel == $nTargetLevel)
            return $this->_aCommonContainer;

        $aData = array();
        foreach ($this->_aCommonContainer as $aRecord)
            if ($nTargetLevel & $aRecord['level'])
                $aData[] = $aRecord;

        return $aData;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Append save provider to log by reference.
     * Without cloning for greater speed of execution.
     *
     * All records from common list adds to list of provider by provider log level. 
     *
     * @param LogProvider $cProvider   Provider to add.
     * @param int         $nErrorLevel Error level for this provider. By default, equal to base.
     *
     * @return void
     */
    public function appendProvider(LogProvider $cProvider, $nErrorLevel = null)
    {
        if (!$nErrorLevel) {
            // If providwer level is not set, log level of provider is equal to base log level.
            // All common records correspond to provider.
            $nLevel = $this->_nCommonLogLevel;
            $aRecords = $this->_aCommonContainer;
        } else {
            // Log level of provider is different to base log level.
            // Filter common records with provider log level.
            $nLevel = $nErrorLevel;
            $aRecords = $this->_getFilterdRecords($nLevel);
        }
        
        $this->_aProviders[] = array(
            'provider' => $cProvider,
            'level' => $nLevel,
            'records' => $aRecords
        );
        
    } // End function
    //-----------------------------------------------------------------------------
    
} // End
//-----------------------------------------------------------------------------