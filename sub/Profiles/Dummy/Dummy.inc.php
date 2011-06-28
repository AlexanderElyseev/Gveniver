<?php
/**
 * File contains dummy profile class.
 *
 * @category   Gveniver
 * @package    Kernel
 * @subpackage Profile
 * @author     Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright  2008-2011 Elyseev Alexander
 * @license    http://prof-club.ru/license.txt Prof-Club License
 * @link       http://prof-club.ru
 */

namespace Gveniver\Kernel;

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
class DummyProfile extends Profile
{
    /**
     * Start profile logic.
     *
     * @return string
     */
    public function start()
    {
        $this->getApplication()->trace->addLine('[%s] Start.', __CLASS__);
        $sResult = $this->getApplication()->template->parseTemplate(
            $this->getMainTemplate(
                $this->getCurrentSectionName(),
                $this->getCurrentAction()
            )
        );
        $this->getApplication()->trace->addLine('[%s] End.', __CLASS__);
        return $sResult;

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------