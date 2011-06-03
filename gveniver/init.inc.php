<?php
/**
 * System initialization splitter.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
define('GV_EXEC', 1);                                            // Execution flag.
define('GV_DS', DIRECTORY_SEPARATOR);                            // Directory separator.
define('GV_PATH_BASE', dirname(__FILE__).GV_DS);                // Gveniver base path.
define('GV_PATH_CACHE', GV_PATH_BASE.'cache'.GV_DS);            // Server cache directory path.
define('GV_PATH_PROFILE', GV_PATH_BASE.'profile'.GV_DS);        // Server profile directory path.
define('GV_DATE_NOW', date('Y-m-d H:i:s'));                        // Current date as string.
define('GV_TIME_NOW', time());                                    // Current time at integer.
define('GV_OS_WIN', stripos(PHP_OS, 'win') !== false);          // Is running on Windows?
define('GV_CLI', stripos(php_sapi_name(), 'cli') !== false);    // Is running on CLI?
define('GV_EOL', GV_CLI ? "\n" : '<br/>');                      // End of line.

require 'system/GvConst.inc.php';
require 'system/GvInclude.inc.php';
require 'system/GvExtendCommon.inc.php';
require 'system/GvKernelConfig.inc.php';
require 'system/GvKernel.inc.php';
require 'system/GvKernelModule.inc.php';
require 'system/GvKernelProfile.inc.php';
require 'system/GvKernelExtension.inc.php';

GvInclude::instance()->skipFile('system/GvConst.inc.php');
GvInclude::instance()->skipFile('system/GvExtendCommon.inc.php');
GvInclude::instance()->skipFile('system/GvInclude.inc.php');
GvInclude::instance()->skipFile('system/GvKernelConfig.inc.php');
GvInclude::instance()->skipFile('system/GvKernel.inc.php');
GvInclude::instance()->skipFile('system/GvKernelModule.inc.php');
GvInclude::instance()->skipFile('system/GvKernelProfile.inc.php');
GvInclude::instance()->skipFile('system/GvKernelExtension.inc.php');

GvInclude::instance()->includeFile('system/exception/ArgumentException.inc.php');
GvInclude::instance()->includeFile('system/exception/NotImplementedException.inc.php');
GvInclude::instance()->includeFile('system/exception/SqlException.inc.php');