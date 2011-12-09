<?php
/**
 * File contains extension class for security actions.
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
 * Extension class for security actions.
 *
 * @category  Gveniver
 * @package   Extension
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class GvSecurityExt extends SimpleExtension
{
    /**
     * Add checkpoint to security system.
     * Prevent XSRF.
     *
     * @param string $sName Name of checkpoint.
     * @param int    $nTtl  Time to live for checkpoint (in seconds).
     *
     * @return string Token key of checkpont.
     */
    public function addCheckPoint($sName = null, $nTtl = null)
    {
        return $this->getApplication()->security->addCheckPoint($sName, $nTtl);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Try to release checkpoint by data from user.
     * Prevent XSRF.
     *
     * @param string $sToken Token, specified by user.
     * @param string $sName  Name of checkpoint.
     *
     * @return bool
     */
    public function releaseCheckPoint($sToken, $sName = null)
    {
        return $this->getApplication()->security->releaseCheckPoint($sToken, $sName);

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------