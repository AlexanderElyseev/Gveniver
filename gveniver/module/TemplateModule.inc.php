<?php
/**
 * File contains template kernel module class.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

namespace Gveniver\Kernel;
\Gveniver\Loader::i('Module.inc.php');

/**
 * Template kernel module class.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class TemplateModule extends Module
{
    /**
     * Current factory for templating system.
     *
     * @var BaseTemplateFactory
     */
    private $_cFactory;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Full initialization of kernel module.
     *
     * @return bool True on success.
     */
    protected function init()
    {
        $this->cKernel->trace->addLine('[%s] Init.', __CLASS__);
        
        // Load factory for template subsystem.
        $this->_cFactory = \Gveniver\Loader::createObject(
            array(
                'class' => $this->cKernel->cConfig->get('Module/TemplateModule/FactoryClass'),
                'ns'    => '\\Gveniver\\',
                'path'  => 'system/template/factory/%class%.inc.php',
                'args'  => array($this->cKernel)
            ),
            $nErrCode
        );
        if (!$this->_cFactory) {
             $this->cKernel->trace->addLine(
                 '[%s] Error in create template factory, with code: %d ("%s").',
                 __CLASS__,
                 $nErrCode,
                 \Gveniver\Loader::getErrorInfo($nErrCode)
             );
            return false;
        }

        $this->cKernel->trace->addLine('[%s] Init sucessful.', __CLASS__);
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
     * @return BaseTemplate|boolean Returns template instance or boolean result of
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
     * If need to parse template object, use {@see BaseTemplate::parse}.
     *
     * @param string $sTemplateName Name of template to parse.
     * @param array  $aData         Template data.
     *
     * @return string|null Returns result of parsing or null, if template not loaded.
     */
    public function parseTemplate($sTemplateName, array $aData = array())
    {
        $this->cKernel->trace->addLine('[%s] Start parse template ("%s").', __CLASS__, $sTemplateName);

        $cTpl = $this->getTemplate($sTemplateName);
        if (!$cTpl) {
            $this->cKernel->trace->addLine(
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