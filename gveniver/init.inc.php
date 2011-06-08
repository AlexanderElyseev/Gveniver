<?php
/**
 * System initialization file.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
define('GV_EXEC', 1);                                           // Execution flag.
define('GV_DS', DIRECTORY_SEPARATOR);                           // Directory separator.
define('GV_PATH_BASE', dirname(__FILE__).GV_DS);                // Gveniver base path.
define('GV_PATH_CACHE', GV_PATH_BASE.'cache'.GV_DS);            // Server cache directory path.
define('GV_PATH_PROFILE', GV_PATH_BASE.'profile'.GV_DS);        // Server profile directory path.
define('GV_DATE_NOW', date('Y-m-d H:i:s'));                     // Current date as string.
define('GV_TIME_NOW', time());                                  // Current time at integer.
define('GV_OS_WIN', stripos(PHP_OS, 'win') !== false);          // Is running on Windows?
define('GV_CLI', stripos(php_sapi_name(), 'cli') !== false);    // Is running on CLI?
define('GV_EOL', GV_CLI ? "\n" : '<br/>');                      // End of line.

require 'system/GvInclude.inc.php';
GvInclude::s('system/GvInclude.inc.php');
require 'system/GvConst.inc.php';
GvInclude::s('system/GvConst.inc.php');
require 'system/GvExtendCommon.inc.php';
GvInclude::s('system/GvExtendCommon.inc.php');
require 'system/GvConfig.inc.php';
GvInclude::s('system/GvConfig.inc.php');
require 'system/GvKernel.inc.php';
GvInclude::s('system/GvKernel.inc.php');
require 'system/GvKernelModule.inc.php';
GvInclude::s('system/GvKernelModule.inc.php');
require 'system/GvKernelProfile.inc.php';
GvInclude::s('system/GvKernelProfile.inc.php');
require 'system/GvKernelExtension.inc.php';
GvInclude::s('system/GvKernelExtension.inc.php');

GvInclude::i('system/exception/ArgumentException.inc.php');
GvInclude::i('system/exception/NotImplementedException.inc.php');
GvInclude::i('system/exception/SqlException.inc.php');