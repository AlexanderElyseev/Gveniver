<?php
/**
 * File contains base abstract controller class for CAPTCHA.
 *
 * @category  Gveniver
 * @package   BaseCaptcha
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

namespace Gveniver\Captcha;

/**
 * Base abstract controller class for CAPTCHA.
 *
 * @category  Gveniver
 * @package   BaseCaptcha
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
abstract class BaseCaptcha
{
    /**
     * Display CAPTCHA for user.
     *
     * @return string
     * @abstract
     */
    public abstract function display();
    //-----------------------------------------------------------------------------

    /**
     * Check answer of user.
     *
     * @return boolean
     * @abstract
     */
    public abstract function check();
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------