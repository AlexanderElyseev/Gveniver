<?php
/**
 * File contains base abstract kernel profile class.
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
 * Base abstract kernel profile class.
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
abstract class GvKernelProfile
{
    /**
     * Reference to current kernel.
     *
     * @var GvKernel
     */
    protected $cKernel;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Constructor of {@see GvKernelProfile} class.
     * Initialize new instance of profile by kernel.
     *
     * @param GvKernel $cKernel Kernel of profile.
     */
    public function __construct(GvKernel $cKernel)
    {
        $this->cKernel = $cKernel;
        
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Start profile logic.
     *
     * @return string
     * @abstract
     */
    public abstract function start();
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------