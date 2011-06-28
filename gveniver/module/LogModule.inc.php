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

namespace Gveniver\Kernel;
\Gveniver\Loader::i('Module.inc.php');
\Gveniver\Loader::i('system/log/Log.inc.php');

/**
 * module class for log subsystem.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class LogModule extends Module
{
    /**
     * Logger object.
     *
     * @var Log
     */
    private $_cLog;
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
                $sClassName = $aProviderData['Class'];
                $nLogProvider = isset($aProviderData['Level']) ? $aProviderData['Level'] : null;
                $cProvider = \Gveniver\Loader::createObject(
                    array(
                        'class' => $sClassName,
                        'ns'    => '\\Gveniver\\Log',
                        'path'  => 'system/log/provider/%class%.inc.php',
                        'args'  => array($this->getApplication(), $aProviderData)
                    ),
                    $nErrCode
                );
                if (!$cProvider) {
                     $this->getApplication()->trace->addLine(
                         '[%s] Error in create log provider ("%s"), with code: %d.',
                         __CLASS__,
                         $sClassName,
                         $nErrCode
                     );
                    continue;

                } // End if

                $this->getApplication()->trace->addLine('[%s] Log provider ("%s") successfully created.', __CLASS__, $sClassName);

                // Add provider to log object.
                $this->_cLog->appendProvider($cProvider, $nLogProvider);

            } // End foreach

        } // End if

        $this->getApplication()->trace->addLine('[%s] Init sucessful.', __CLASS__);
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