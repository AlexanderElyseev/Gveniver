<?php
/**
 * File contains log provider class for saving log data to file.
 *
 * @category  Gveniver
 * @package   Log
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

GvKernelInclude::instance()->includeFile('gveniver/system/log/provider/LogProvider.inc.php');

/**
 * Log provider class for saving log data to file.
 *
 * @category  Gveniver
 * @package   Log
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class FileLogProvider extends LogProvider
{
    /**
     * Absolute path to log file.
     * 
     * @var string.
     */
    private $_sFileName;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Class constructor.
     * Initialize member fields.
     *
     * @param GvKernel $cKernel     Current kernel.
     * @param array    $aConfigData Configuration data for provider.
     *
     * @return void
     */
    public function __construct(GvKernel $cKernel, array $aConfigData)
    {
        // Use parent constructor.
        parent::__construct($cKernel, $aConfigData);

        if (!isset($aConfigData['FileName']) || !is_string($aConfigData['FileName']))
            throw new GvException('Log file name must be specified.');
        
        $this->_sFileName = $aConfigData['FileName'];

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Save log data to specified log file.
     * 
     * @param array $aData Data to save.
     * 
     * @return void
     */
    public function save(array $aData)
    {
        // Convert to absolute path.
        if (!GvKernelInclude::isAbsolutePath($this->_sFileName))
            $this->_sFileName = GV_PATH_BASE.$this->_sFileName;

        // Check permissions to write in log directory.
        $sLogDir = dirname($this->_sFileName);
        if (!is_dir($sLogDir) || !is_writable($sLogDir)) {
            if (!mkdir($sLogDir, 0777, true)) {
                $this->cKernel->trace->addLine('[%s] Permissions denied in writeing log  at "%s".', __CLASS__, $sLogDir);
                return;
            }
        }

        // Open file for writing.
        $cFile = fopen($this->_sFileName, 'a');
        if (!$cFile) {
            $this->cKernel->trace->addLine('[%s] Error in opening log "%s".', __CLASS__, $this->_sFileName);
            return;
        }

        // Locking for writing.
        if (flock($cFile, LOCK_EX)) {
            foreach ($aData as $aLog)
                fwrite(
                    $cFile,
                    sprintf(
                        "[%s] - %s - %d - %s - %s\n",
                        date('Y-m-d H:i:s', $aLog['time']),
                        self::getNameByLevel($aLog['level']),
                        $aLog['code'],
                        $aLog['message'],
                        serialize($aLog['data'])
                    )
                );
            
            flock($cFile, LOCK_UN);

        } // End if

        fclose($cFile);
        
    } // End function
    //-----------------------------------------------------------------------------
    
} // End class
//-----------------------------------------------------------------------------