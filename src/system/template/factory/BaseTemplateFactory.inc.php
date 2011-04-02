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
 * TODO: Cache of templates.
 *
 * @category  Gveniver
 * @package   Template
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
abstract class BaseTemplateFactory
{
    /**
     * Cache of loaded templates.
     *
     * @var array
     */
    private $_aTemplateCache = array();
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Base constructor.
     */
    public function __construct()
    {
    } // End function
    //-----------------------------------------------------------------------------

	/**
	 * Load template by template name.
	 *
	 * @param string $sTemplateName Name of template for loading.
	 *
	 * @return BaseTemplate
	 */
	public function load($sTemplateName)
    {
        return array_key_exists($sTemplateName, $this->_aTemplateCache)
            ? ($this->_aTemplateCache[$sTemplateName])
            : ($this->_aTemplateCache[$sTemplateName] = $this->build($sTemplateName));
        
    } // End function
	//-----------------------------------------------------------------------------

    /**
     * Build template by template name.
     * Must implements specific actions for specific template types.
     *
     * @param string $sTemplateName Name of template for building.
     *
     * @return BaseTemplate
     * @abstract
     */
    protected abstract function build($sTemplateName);
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------