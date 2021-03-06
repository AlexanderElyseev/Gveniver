<?php
/**
 * File contains template class for Smarty template system.
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
 * Template class for Smarty template system.
 *
 * @category  Gveniver
 * @package   Template
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class Smarty3Template extends BaseTemplate
{
    /**
     * Smarty compiled template.
     *
     * @var \Smarty
     */
    private $_cTpl;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Class constructor.
     * Initialize template by smarty object and template name.
     *
     * @param \Smarty &$cSmarty      Base Smarty object for initialization.
     * @param string  $sTemplateName Full path to template file.
     */
    public function __construct(\Smarty &$cSmarty, $sTemplateName)
    {
        $this->_cTpl = $cSmarty->createTemplate($sTemplateName);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Parse template with specified data.
     *
     * @param array $aTemplateData Data for template.
     *
     * @return string
     */
    public function parse(array $aTemplateData)
    {
        $this->_cTpl->clearAllAssign();
        $this->_cTpl->assign($aTemplateData);
        return $this->_cTpl->fetch($this->_cTpl);
        
    } // End function
    //-----------------------------------------------------------------------------
    
} // End class
//-----------------------------------------------------------------------------