<?php
/**
 * File contains base abstract class for template objects factory.
 *
 * @category  Gveniver
 * @package   Template
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

namespace Gveniver\Template\Factory;

/**
 * Base abstract class for template objects factory.
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
     * Current application.
     * 
     * @var \Gveniver\Kernel\Application
     */
    private $_cApplication;
    //-----------------------------------------------------------------------------

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
     * Initialize member fields.
     *
     * @param \Gveniver\Kernel\Application $cApplication Current application.
     */
    public function __construct(\Gveniver\Kernel\Application $cApplication)
    {
        $this->_cApplication = $cApplication;
        
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Getter for current application.
     *
     * @return \Gveniver\Kernel\Application
     */
    public function getApplication()
    {
        return $this->_cApplication;

    } // End function
    //-----------------------------------------------------------------------------
    
    /**
     * Load template by template name.
     *
     * @param string $sTemplateName Name of template for loading.
     *
     * @return \Gveniver\Template\BaseTemplate
     */
    public function load($sTemplateName)
    {
        return array_key_exists($sTemplateName, $this->_aTemplateCache)
            ? $this->_aTemplateCache[$sTemplateName]
            : $this->_aTemplateCache[$sTemplateName] = $this->build($sTemplateName);
        
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Build template by name.
     * Implements specific actions for specific template types.
     *
     * @param string $sTemplateName Name of template for building.
     *
     * @return \Gveniver\Template\BaseTemplate
     * @abstract
     */
    protected abstract function build($sTemplateName);
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------