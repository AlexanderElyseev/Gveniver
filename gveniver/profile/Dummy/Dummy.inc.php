<?php
/**
 * Filec ontains dummy profile class.
 *
 * @category   Gveniver
 * @package    Kernel
 * @subpackage Profile
 * @author     Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright  2008-2011 Elyseev Alexander
 * @license    http://prof-club.ru/license.txt Prof-Club License
 * @link       http://prof-club.ru
 */

/**
 * Dummy profile class.
 *
 * PHP version 5
 *
 * @category   Gveniver
 * @package    Kernel
 * @subpackage Profile
 * @author     Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright  2008-2011 Elyseev Alexander
 * @license    http://prof-club.ru/license.txt Prof-Club License
 * @link       http://prof-club.ru
 */
class DummyKernelProfile extends GvKernelProfile
{
    /**
     * Start profile logic.
     *
     * @return string
     */
    public function start()
    {
        $this->cKernel->trace->addLine('[%s] Start.', __CLASS__);
        return $this->cKernel->template->parseTemplate('main_enter', array('fromarray' => 'data'));

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------