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
     * @return string
     */
    public function getTrace()
    {
        return $this->cKernel->trace->getTraceAsString();

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