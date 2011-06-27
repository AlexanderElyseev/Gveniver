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
class DummyKernelProfile extends Profile
{
    /**
     * Start profile logic.
     *
     * @return string
     */
    public function start()
    {
        $this->cKernel->trace->addLine('[%s] Start.', __CLASS__);
        $sResult = $this->cKernel->template->parseTemplate(
            $this->getMainTemplate(
                $this->getCurrentSectionName(),
                $this->getCurrentAction()
            )
        );
        $this->cKernel->trace->addLine('[%s] End.', __CLASS__);
        return $sResult;

    } // End function
    //-----------------------------------------------------------------------------
    
    /**
     * Return name of current section.
     *
     * @return string
     */
    public function getCurrentSectionName()
    {
        return $this->cKernel->invar->get(
            $this->cKernel->cConfig->get('Module/InvarModule/SectionKeyName')
        );
        
    } // End function
    //-----------------------------------------------------------------------------
    
    /**
     * Returns value of current action.
     *
     * @return string
     */
    public function getCurrentAction()
    {
        return $this->cKernel->invar->get(
            $this->cKernel->cConfig->get('Module/InvarModule/ActionKeyName')
        );

    } // End function
    //-----------------------------------------------------------------------------
    
} // End class
//-----------------------------------------------------------------------------