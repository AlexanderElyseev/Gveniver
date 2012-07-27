<?php
/**
 * File contains extension class for debugging.
 *
 * @category  Gveniver
 * @package   Extension
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

namespace Gveniver\Extension;

/**
 * Extension class for debugging.
 *
 * @category  Gveniver
 * @package   Extension
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class GvDebugExt extends SimpleExtension
{
    /**
     * Returns current trace content from tracing module.
     *  
     * @return array
     */
    public function getTrace()
    {
        return $this->getApplication()->trace->getTrace();

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Return all current trace information as text string.
     *
     * @return string
     */
    public function getTraceAsString()
    {
        $sRet = '';
        foreach ($this->getTrace() as $aMessage)
            $sRet .= sprintf(
                '[%s %s s. (+%s s.), %.2f KiB (%s%.2f KiB)] %s%s',
                $aMessage['time'],
                round($aMessage['dtime'], 4),
                round($aMessage['etime'], 4),
                $aMessage['memory'] / 1024,
                $aMessage['ememory'] >= 0 ? '+' : '',
                $aMessage['ememory'] / 1024,
                $aMessage['text'],
                GV_EOL
            );

        return $sRet;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Return value of debug state flag.
     *
     * @return bool True if debug is enabled.
     */
    public function isDebug()
    {
        return \Gveniver\toBoolean($this->getApplication()->getConfig()->get('Kernel/Debug'));
        
    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------