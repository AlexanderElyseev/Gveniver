<?php
/**
 * File contains base abstract class for templates, used in system.
 *
 * @category  Gveniver
 * @package   Template
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

namespace Gveniver\Template;

/**
 * Base abstract class for templates, used in system.
 *
 * @category  Gveniver
 * @package   Template
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 * @abstract
 */
abstract class Template
{
    /**
     * Parse template with specified data.
     *
     * @param array $aTemplateData Data for template.
     *
     * @return string
     * @abstract
     */
    public abstract function parse(array $aTemplateData);
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------