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

namespace Gveniver\Log\Provider;

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
class FileLogProvider extends BaseProvider
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
     * @param \Gveniver\Kernel\Application $cApplication Current application.
     * @param array                        $aConfigData  Configuration data for provider.
     */
    public function __construct(\Gveniver\Kernel\Application $cApplication, array $aConfigData)
    {
        // Use parent constructor.
        parent::__construct($cApplication, $aConfigData);

        if (!isset($aConfigData['FileName']) || !is_string($aConfigData['FileName']))
            throw new \Gveniver\Exception\Exception('Log file name must be specified.');

        $bRelativeByProfile = isset($aConfigData['RelativeByProfile'])
            && \Gveniver\Kernel\Application::toBoolean($aConfigData['RelativeByProfile']);
    
        $this->_sFileName = $aConfigData['FileName'];

        // Convert to absolute path.
        if ($bRelativeByProfile)
            $this->_sFileName = $cApplication->getProfile()->getPath().$this->_sFileName;
        elseif (!\Gveniver\Loader::isAbsolutePath($this->_sFileName))
            $this->_sFileName = GV_PATH_BASE.$this->_sFileName;
        
        $this->_sFileName = \Gveniver\correctPath($this->_sFileName);

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
        // Check permissions to write in log directory.
        $sLogDir = dirname($this->_sFileName);
        if (!is_dir($sLogDir) || !is_writable($sLogDir)) {
            if (!mkdir($sLogDir, 0777, true)) {
                $this->getApplication()->trace->addLine(
                    '[%s] Permissions denied in writeing log  at "%s".',
                    __CLASS__,
                    $sLogDir
                );
                return;
            }
        }

        // Open file for writing.
        $cFile = fopen($this->_sFileName, 'a');
        if (!$cFile) {
            $this->getApplication()->trace->addLine(
                '[%s] Error in opening log "%s".',
                __CLASS__,
                $this->_sFileName
            );
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