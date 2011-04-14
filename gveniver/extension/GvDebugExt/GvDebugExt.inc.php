<?php
/**
 *
 *
 * @category  Gveniver
 * @package   Extension
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

GvInclude::instance()->includeFile('gveniver/system/extension/SimpleExtension.inc.php');

/**
 *
 *
 * @category  Gveniver
 * @package   Extension
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class GvDebugExt extends SimpleExtenson
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

} // End class
//-----------------------------------------------------------------------------