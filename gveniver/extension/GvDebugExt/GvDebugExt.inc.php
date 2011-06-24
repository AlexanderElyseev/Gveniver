<?php
/**
 * File contains kernel extension class for debugging.
 *
 * @category  Gveniver
 * @package   Extension
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

namespace Gveniver\Extension;
\Gveniver\Loader::i('system/extension/SimpleExtension.inc.php');

/**
 * Kernel extension class for debugging.
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
        return $this->cKernel->trace->getTrace();

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
        return $this->cKernel->toBoolean(
            $this->cKernel->cConfig->get('Kernel/Debug')
        );
        
    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------