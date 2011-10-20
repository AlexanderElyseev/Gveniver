<?php
/**
 * File contains module class for log subsystem.
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
 * Module class for log subsystem.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 * 
 * TODO: Types of arguments.
 * @method fatalError($message, $data = null, $code = null) Log for fatal error.
 * @method error($message, $data = null, $code = null)      Log for error.
 * @method security($message, $data = null, $code = null)   Log for security accident.
 * @method warning($message, $data = null, $code = null)    Log for warning.
 * @method info($message, $data = null, $code = null)       Log for information message.
 */
class LogModule extends BaseModule
{
    /**
     * Logger object.
     *
     * @var \Gveniver\Log\Log
     */
    private $_cLog;
    //-------------------------------------------------------------------------------
    //-------------------------------------------------------------------------------

    /**
     * Full initialization of module.
     *
     * @return bool True on success.
     */
    protected function init()
    {
        $this->getApplication()->trace->addLine('[%s] Init.', __CLASS__);

        // If log configuration is specified, builld log object and load providers configuration.
        $nCommonLogLevel = intval($this->getApplication()->getConfig()->get('Module/LogModule/Level'));
        if (!$nCommonLogLevel) {
            $this->getApplication()->trace->addLine('[%s] Log configuration not found.', __CLASS__);
            return true;
        }
        
        $this->getApplication()->trace->addLine('[%s] Common log level: %d.', __CLASS__, $nCommonLogLevel);

        // Build base log object.
        $this->_cLog = new \Gveniver\Log\Log($nCommonLogLevel);

         // Load configuration of log providers.
        $aProviders = $this->getApplication()->getConfig()->get('Module/LogModule/Providers');
        if (is_array($aProviders)) {
            foreach ($aProviders as $aProviderData) {

                // Class name of log provider is required.
                if (!isset($aProviderData['Class'])) {
                    $this->getApplication()->trace->addLine('[%s] Provider class name is not specified.', __CLASS__);
                    continue;
                }

                // Load provider object dynamically.
                $sClassName = '\\Gveniver\\Log\\Provider\\'.$aProviderData['Class'];
                $nLogProvider = isset($aProviderData['Level']) ? $aProviderData['Level'] : null;
                $cProvider = new $sClassName($this->getApplication(), $aProviderData);
                if (!$cProvider) {
                     $this->getApplication()->trace->addLine(
                         '[%s] Error in create log provider ("%s").',
                         __CLASS__,
                         $aProviderData['Class']
                     );
                    continue;

                } // End if

                $this->getApplication()->trace->addLine('[%s] Log provider ("%s") successfully created.', __CLASS__, $sClassName);

                // Add provider to log object.
                $this->_cLog->appendProvider($cProvider, $nLogProvider);

            } // End foreach

        } // End if

        $this->getApplication()->trace->addLine('[%s] Init successful.', __CLASS__);
        return true;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Class destructor.
     * Save log data before delete.
     *
     * @return void
     */
    public function __destruct()
    {
        if ($this->_cLog)
            $this->_cLog->save();

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Each call of module methods is redirected to log object.
     *
     * @param string $name      Method name.
     * @param array  $arguments Method arguments.
     *
     * @return void
     */
    public function __call($name, array $arguments)
    {
        if ($this->_cLog)
            call_user_func_array(array($this->_cLog, $name), $arguments);

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------