<?php
/**
 * File contains log provider class for saving log data to some PHP stream.
 *
 * @category  Gveniver
 * @package   Log
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

GvInclude::i('system/log/provider/LogProvider.inc.php');

/**
 * Log provider class for saving log data to some PHP stream.
 *
 * @category  Gveniver
 * @package   Log
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class StreamLogProvider extends LogProvider
{
    /**
     * Name of stream for saving log data.
     *
     * @var string.
     */
    private $_sStreamName;
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

        if (!isset($aConfigData['StreamName']) || !is_string($aConfigData['StreamName']))
            throw new GvException('Log stream name must be specified.');
        
        $this->_sStreamName = $aConfigData['StreamName'];

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Save log data to specified stream.
     * 
     * @param array $aData Data to save.
     * 
     * @return void
     */
    public function save(array $aData)
    {
        $cFile = fopen($this->_sStreamName, 'a');
        if (!$cFile) {
            $this->cKernel->trace->addLine('[%s] Error in opening log stream "%s".', __CLASS__, $this->_sStreamName);
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