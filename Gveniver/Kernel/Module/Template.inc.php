<?php
/**
 * File contains template module class.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

namespace Gveniver\Kernel\Module;

/**
 * Template module class.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class Template extends BaseModule
{
    /**
     * Current factory for templating system.
     *
     * @var \Gveniver\Template\Factory\BaseFactory
     */
    private $_cFactory;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Full initialization of module.
     *
     * @return bool True on success.
     */
    protected function init()
    {
        $this->getApplication()->trace->addLine('[%s] Init.', __CLASS__);
        
        // Load factory for template subsystem.
        $sFactoryClassName = $this->getApplication()->getConfig()->get('Module/TemplateModule/FactoryClass');
        $sFactoryClassName = '\\Gveniver\\Template\\Factory\\'.$sFactoryClassName;
        $this->_cFactory = new $sFactoryClassName($this->getApplication());
        if (!$this->_cFactory) {
             $this->getApplication()->trace->addLine(
                 '[%s] Error in create template factory.',
                 __CLASS__
             );
            return false;
        }

        $this->getApplication()->trace->addLine('[%s] Init successful.', __CLASS__);
        return true;

    } // End function
    //-----------------------------------------------------------------------------
    
    /**
     * Load template by name.
     *
     * @param string $sTemplateName Name of template for loading.
     * @param mixed  &$cRef         Reference variable for loading template.
     * If specified, then the template loads to variable by refernce and returns
     * result of operation (boolean). Otherwise, returns template.
     *
     * @return \BaseTemplate\Template\Template|boolean Returns template instance or boolean result of
     * loading operation if specified reference varaible.
     */
    public function getTemplate($sTemplateName, &$cRef = null)
    {
        // Load template with factory by name.
        $cTpl = $this->_cFactory->load($sTemplateName);

        // Load by reference.
        if (func_num_args() > 1) {
            if (!$cTpl)
                return false;

            $cRef = $cTpl;
            return true;

        } // End if

        return $cTpl;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Parse template by template name and template data.
     * If need to parse template object, use {@see Template::parse}.
     *
     * @param string $sTemplateName Name of template to parse.
     * @param array  $aData         Template data.
     *
     * @return string|null Returns result of parsing or null, if template not loaded.
     */
    public function parseTemplate($sTemplateName, array $aData = array())
    {
        $this->getApplication()->trace->addLine('[%s] Start parse template ("%s").', __CLASS__, $sTemplateName);

        $cTpl = $this->getTemplate($sTemplateName);
        if (!$cTpl) {
            $this->getApplication()->trace->addLine(
                '[%s] Template ("%s") not found.',
                __CLASS__,
                $sTemplateName
            );
            return null;
        }

        return $cTpl->parse($aData);

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------