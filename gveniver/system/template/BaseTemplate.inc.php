<?php
/**
 *
 *
 * @category  Gveniver
 * @package   Template
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

/**
 *
 *
 * @category  Gveniver
 * @package   Template
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
abstract class BaseTemplate
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