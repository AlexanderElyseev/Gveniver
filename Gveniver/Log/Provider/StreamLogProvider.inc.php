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

namespace Gveniver\Log\Provider;

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
class StreamLogProvider extends BaseLogProvider
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
     * @param \Gveniver\Kernel\Application $cApplication Current application.
     * @param array                        $aConfigData  Configuration data for provider.
     */
    public function __construct(\Gveniver\Kernel\Application $cApplication, array $aConfigData)
    {
        // Use parent constructor.
        parent::__construct($cApplication, $aConfigData);

        if (!isset($aConfigData['StreamName']) || !is_string($aConfigData['StreamName']))
            throw new \Gveniver\Exception\BaseException('Log stream name must be specified.');
        
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
            $this->getApplication()->trace->addLine('[%s] Error in opening log stream "%s".', __CLASS__, $this->_sStreamName);
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