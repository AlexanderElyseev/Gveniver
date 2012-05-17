<?php
/**
 * Bootstrap file for unit testing.
 *
 * @category  Gveniver
 * @package   UnitTest
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
/** @noinspection PhpIncludeInspection */
require_once '..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'init.inc.php';

define('TEST_PROFILE_PATH', dirname(__DIR__).DIRECTORY_SEPARATOR.'Profiles'.DIRECTORY_SEPARATOR.'Dummy'.DIRECTORY_SEPARATOR);

/**
 * Returns application by profile and configuration.
 *
 * @param mixed $mConfig Configuration of application.
 *
 * @return Gveniver\Kernel\Application
 */
function getApplication($mConfig = null)
{
    $hash = md5(serialize(func_get_args()));
    static $app = array();
    if (!isset($app[$hash]))
        $app[$hash] = new Gveniver\Kernel\Application(TEST_PROFILE_PATH, $mConfig);

    return $app[$hash];
}