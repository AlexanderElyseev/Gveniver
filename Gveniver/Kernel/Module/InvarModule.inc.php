<?php
/**
 * File contains invar module class.
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
 * Invar module class.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class InvarModule extends BaseModule
{
    /**
     * Target for loading invar parameter for request.
     * Load only from GET.
     *
     * @var int
     */
    const TARGET_ONLY_GET = 1;
    //-----------------------------------------------------------------------------

    /**
     * Target for loading invar parameter for request.
     * Load only from request string.
     *
     * @var int
     */
    const TARGET_ONLY_REQUEST = 2;
    //-----------------------------------------------------------------------------

    /**
     * Target for loading invar parameter for request.
     * Load from GET, then from request string.
     *
     * @var int
     */
    const TARGET_FIRST_GET = 3;
    //-----------------------------------------------------------------------------

    /**
     * Target for loading invar parameter for request.
     * Load from request string, then from GET.
     *
     * @var int
     */
    const TARGET_FIRST_REQUEST = 4;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Loader for invars.
     *
     * @var \Gveniver\Invar\Loader\BaseInvarLoader
     */
    private $_cLoader;
    //-----------------------------------------------------------------------------

    /**
     * Analyzed invars from GET.
     *
     * @var array
     */
    private $_aGet = array();
    //-----------------------------------------------------------------------------

    /**
     * Analyzed invars from POST.
     *
     * @var array
     */
    private $_aPost = array();
    //-----------------------------------------------------------------------------

    /**
     * Section key name in invars.
     * 
     * @var string
     */
    private $_sSectionKey;
    //-----------------------------------------------------------------------------

    /**
     * Section key template for replacements.
     *
     * @var string
     */
    private $_sSectionKeyTpl;
    //-----------------------------------------------------------------------------

    /**
     * Action key name in invars.
     *
     * @var string
     */
    private $_sActionKey;
    //-----------------------------------------------------------------------------

    /**
     * Action key template for replacements.
     *
     * @var string
     */
    private $_sActionKeyTpl;
    //-----------------------------------------------------------------------------

    /**
     * Abolute web path to root.
     *
     * @var string
     */
    private $_sAbsWebPath;
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
        $sLoaderClassName = '\\Gveniver\\Invar\\Loader\\';
        $sLoaderClassName .= $this->getApplication()->getConfig()->get('Module/InvarModule/LoaderClass');

        $this->_cLoader = new $sLoaderClassName();
        if (!$this->_cLoader) {
            $this->getApplication()->trace->addLine(
                '[%s] Error in create invar loader.',
                __CLASS__
            );
            return false;
        }

        // Initialize arrays of invars.
        $this->_aGet['request'] = $this->_cLoader->analyzeRequest();
        $this->_aGet['get'] = &$_GET;
        $this->_aGet['checked'] = array();

        $this->_aPost['post'] = &$_POST;
        $this->_aPost['checked'] = array();

        // Load settings.
        $this->_sSectionKey = $this->getApplication()->getConfig()->get('Module/InvarModule/SectionKeyName');
        $this->_sSectionKeyTpl = $this->getApplication()->getConfig()->get('Module/InvarModule/SectionKeyTemplate');
        $this->_sActionKey = $this->getApplication()->getConfig()->get('Module/InvarModule/ActionKeyName');
        $this->_sActionKeyTpl = $this->getApplication()->getConfig()->get('Module/InvarModule/ActionKeyTemplate');
        $this->_sAbsWebPath = $this->getApplication()->getConfig()->get('Profile/Path/AbsRootWeb');
        if (!$this->_sAbsWebPath
            || !$this->_sActionKey || !$this->_sSectionKey
            || !$this->_sActionKeyTpl || !$this->_sSectionKeyTpl
        ) {
            $this->getApplication()->trace->addLine('[%s] Some configuration parameters not loaded.', __CLASS__);
            return false;
        }

        $this->getApplication()->trace->addLine('[%s] Init successful.', __CLASS__);
        return true;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Loading value of invar parameter by name from GET reguest.
     *
     * @param string   $sName   Name of invar for loading value.
     * @param int|null $nTarget Target for loading data.
     *                          If it is null, using TARGET_FIRST_GET flag.
     * @param mixed    &$cRef   Reference variable for loading value.
     *                          If specified, loads result by reference. Returns result of operation.
     *
     * @return bool|mixed If specified reference variable, then returns result of operation.
     * Otherwise, returns value of invar.
     * Returns null, if variable not loaded by name.
     */
    public function get($sName, $nTarget = self::TARGET_FIRST_GET, &$cRef = null)
    {
        $this->getApplication()->trace->addLine('[%s] Load invar ("%s") from GET.', __CLASS__, $sName);

        // Using the default target if it is undefined.
        if (!$nTarget)
            $nTarget = self::TARGET_FIRST_GET;

        $bByRef = func_num_args() == 3;
        $mValue = null;

        // At first, load from GET, then from request.
        if ($nTarget == self::TARGET_FIRST_GET) {
            // Load from GET at first, then load from request.
            if ($this->get($sName, self::TARGET_ONLY_GET, $mValue) || $this->get($sName, self::TARGET_ONLY_REQUEST, $mValue)) {
                if (!$bByRef)
                    return $mValue;

                $cRef = $mValue;
                return true;
            }

            if (!$bByRef)
                return null;

            return false;
        }

        // First, load from request, then from GET.
        if ($nTarget == self::TARGET_FIRST_REQUEST) {
            // Load from GET at first, then load from request.
            if ($this->get($sName, self::TARGET_ONLY_REQUEST, $mValue) || $this->get($sName, self::TARGET_ONLY_GET, $mValue)) {
                if (!$bByRef)
                    return $mValue;

                $cRef = $mValue;
                return true;
            }

            if (!$bByRef)
                return null;

            return false;
        }

        $this->getApplication()->trace->addLine('[%s] Load invar ("%s") from request (%d).', __CLASS__, $sName, $nTarget);

        // Try to load invar value from GET.
        if ($nTarget == self::TARGET_ONLY_GET) {
            if (array_key_exists($sName, $this->_aGet['get'])) {
                $this->getApplication()->trace->addLine('[%s] Invar ("%s") loaded from GET.', __CLASS__, $sName);

                if (!$bByRef)
                    return $this->_aGet['get'][$sName];

                $cRef = $this->_aGet['get'][$sName];
                return true;
            }
            // Try to load invar value from request.
        } elseif ($nTarget == self::TARGET_ONLY_REQUEST) {
            if (array_key_exists($sName, $this->_aGet['request'])) {
                $this->getApplication()->trace->addLine('[%s] Invar ("%s") loaded from request.', __CLASS__, $sName);

                if (!$bByRef)
                    return $this->_aGet['request'][$sName];

                $cRef = $this->_aGet['request'][$sName];
                return true;
            }
        } else {
            $this->getApplication()->trace->addLine(
                '[%s] Invar ("%s") not loaded. Wrong target (%d).',
                __CLASS__,
                $sName,
                $nTarget
            );

            if (!$bByRef)
                return null;

            return false;
        }

        // Invar value not loaded.
        $this->getApplication()->trace->addLine('[%s] Invar ("%s") not loaded from request.', __CLASS__, $sName);

        if (!$bByRef)
            return null;

        return false;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Extended variant of loading value of invar parameter by name from GET reguest.
     *
     * @param string   $sName   Name of invar for loading value.
     * @param int|null $nTarget Target for loading data.
     *                          If it is null, using TARGET_FIRST_GET flag.
     * @param array    $aCheck  Array of parameters for checking.
     * @param mixed    &$cRef   Reference variable for loading value.
     *                          If specified, loads result by reference. Returns result of operation.
     *
     * @return bool|mixed
     */
    public function getEx($sName, $nTarget = self::TARGET_FIRST_GET, array $aCheck = array(), &$cRef = null)
    {
        $this->getApplication()->trace->addLine('[%s] Extended load invar ("%s") from request.', __CLASS__, $sName);

        // Using the default target if it is undefined.
        if (!$nTarget)
            $nTarget = self::TARGET_FIRST_GET;

        $bByRef = func_num_args() == 4;
        $mValue = null;

        // Load value of invar.
        if (!$this->get($sName, $nTarget, $mValue)) {
            if (!$bByRef)
                return null;

            return false;
        }

        // Check value of invar and return result.
        $bCheckResult = $this->_filter($mValue, $aCheck);
        if (!$bCheckResult) {
            $this->getApplication()->trace->addLine('[%s] Filter invar ("%s") failed.', __CLASS__, $sName);
            if ($bByRef)
                return false;

            return null;
        }

        $this->getApplication()->trace->addLine('[%s] Filter invar ("%s") success.', __CLASS__, $sName);

        if ($bByRef) {
            if ($bCheckResult)
                $cRef = $mValue;
            return true;
        }

        return $mValue;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Loading value of invar parameter by name from POST request.
     *
     * @param string $sName Name of invar for loading value.
     * @param mixed  &$cRef Reference variable for loading value.
     *                      If specified, loads result by reference. Returns result of operation.
     *
     * @return bool|mixed
     */
    public function post($sName, &$cRef = null)
    {
        $this->getApplication()->trace->addLine('[%s] Load invar ("%s") from POST.', __CLASS__, $sName);

        $bByRef = func_num_args() == 2;

        // Try to Load from POST.
        if (array_key_exists($sName, $this->_aPost['post'])) {
            $this->getApplication()->trace->addLine('[%s] Invar ("%s") loaded from POST.', __CLASS__, $sName);
            
            if (!$bByRef)
                return $this->_aPost['post'][$sName];

            $cRef = $this->_aPost['post'][$sName];
            return true;
        }

        // Invar value not loaded.
        $this->getApplication()->trace->addLine('[%s] Invar ("%s") not loaded from POST.', __CLASS__, $sName);

        if (!$bByRef)
            return null;

        return false;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Extended variant of loading value of invar parameter by name from POST reguest.
     *
     * @param string $sName  Name of invar for loading value.
     * @param array  $aCheck Array of parameters for checking.
     * @param mixed  &$cRef  Reference variable for loading value.
     *                       If specified, loads result by reference. Returns result of operation.
     *
     * @return bool|mixed
     */
    public function postEx($sName, array $aCheck = array(), &$cRef = null)
    {
        $this->getApplication()->trace->addLine('[%s] Extended load invar ("%s") from POST.', __CLASS__, $sName);

        $bByRef = func_num_args() == 3;
        $mValue = null;

        // Load value of invar.
        if (!$this->post($sName, $mValue)) {
            if (!$bByRef)
                return null;

            return false;
        }

        // Check value of invar and return result.
        $bCheckResult = $this->_filter($mValue, $aCheck);
        if (!$bCheckResult) {
            $this->getApplication()->trace->addLine('[%s] Filter invar ("%s") failed.', __CLASS__, $sName);
            if ($bByRef)
                return false;

            return null;
        }

        $this->getApplication()->trace->addLine('[%s] Filter invar ("%s") success.', __CLASS__, $sName);

        if ($bByRef) {
            if ($bCheckResult)
                $cRef = $mValue;
            return true;
        }

        return $mValue;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Filter value of invar by specified data.
     * Use standart filter_var function with options.
     *
     * Data keys:
     * - "filter"  - Filter name for checking.
     * - "options" - Filter options.
     *
     * @param mixed &$mValue Value of invar for check.
     * @param array $aCheck  Parameters of invar for check.
     *
     * @return boolean
     */
    private function _filter(&$mValue, $aCheck)
    {
        if (!array_key_exists('filter', $aCheck))
            return false;

        $mOptions = array_key_exists('options', $aCheck) ? $aCheck['options'] : null;
        $mValue = filter_var($mValue, $aCheck['filter'], $mOptions);
        return $mValue !== false;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Create link with current invars loader by specified request parameters.
     *
     * @param array $aParams Request arguments.
     *
     * @return string
     */
    public function getLink(array $aParams)
    {
        // Rebuild special placeholders for section and action keys.
        $aRebuildedParams = array();
        foreach ($aParams as $k => $v) {
            $key = $k;
            if ($key == $this->_sSectionKeyTpl)
                $key = $this->_sSectionKey;
            elseif ($key == $this->_sActionKeyTpl)
                $key = $this->_sActionKey;

            $aRebuildedParams[$key] = $v;

        }
        ksort($aRebuildedParams);
        return $this->_sAbsWebPath.$this->_cLoader->buildRequest($aRebuildedParams);

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------