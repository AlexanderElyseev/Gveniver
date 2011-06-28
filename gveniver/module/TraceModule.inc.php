<?php
/**
 * File contains tracing module class.
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
 * Tracing module class.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class TraceModule extends Module
{
    /**
     * List of messages.
     * 
     * @var array
     */
    private $_aMessages;
    //-----------------------------------------------------------------------------
    
    /**
     * Add debug information for messages.
     *
     * @var bool
     */
    private $_bDebug;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Full initialization of module.
     *
     * @return bool True on success.
     */
    protected function init()
    {
        $this->_aMessages = array();
        $this->_bDebug = Application::toBoolean(
            $this->getApplication()->getConfig()->get(
                array('Kernel', 'Debug')
            )
        );

        $this->addLine('[%s] Init sucessful.', __CLASS__);
        return true;
        
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Return all current trace information.
     * 
     * @return array
     */
    public function getTrace()
    {
        if (!$this->_bDebug)
            return array();

        return $this->_aMessages;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Adding trace string.
     * Using sprintf syntax is available.
     * Add data to container only if debug enabled.
     *
     * @param string $sMessage String to add.
     *
     * @return void
     */
    public function addLine($sMessage)
    {
        // Add trace data only in debug state.
        if (!$this->_bDebug)
            return;

        // Build message with sprintf function if specified more than 1 parameter.
        if (func_num_args() > 1) {
            $aArgs = func_get_args();
            $sMessage = call_user_func_array('sprintf', $aArgs);
        }

        //echo $sMessage."<br/>\n";
        
        $dMemory = memory_get_usage();
        $dTime = microtime(true);
        $nPrevIndex = count($this->_aMessages) - 1;
        $this->_aMessages[] = array(
            'text'    => htmlspecialchars($sMessage),
            'time'    => date('Y-m-d H:i:s', $dTime),
            'mtime'   => $dTime,
            'memory'  => $dMemory,
            'etime'   => $nPrevIndex >= 0 ? $dTime - $this->_aMessages[$nPrevIndex]['mtime']: null,
            'dtime'   => $nPrevIndex >= 0 ? $dTime - $this->_aMessages[0]['mtime']: null,
            'ememory' => $nPrevIndex >= 0 ? $dMemory - $this->_aMessages[$nPrevIndex]['memory'] : null,
        );

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------