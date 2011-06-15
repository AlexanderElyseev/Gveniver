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

namespace Gveniver\Template;

/**
 * Base abstract class for template objects factory.
 * 
 * TODO: Cache of templates.
 *
 * @category  Gveniver
 * @package   Template
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
abstract class TemplateFactory
{
    /**
     * Current kernel.
     * 
     * @var Kernel
     */
    protected $cKernel;
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
     * @param \Gveniver\Kernel\Kernel $cKernel Current kernel.
     */
    public function __construct(\Gveniver\Kernel\Kernel $cKernel)
    {
        $this->cKernel = $cKernel;
        
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