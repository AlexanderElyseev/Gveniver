<?php

ob_start();

require_once '..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'init.inc.php';

/**
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 *
 * @return string
 */
function getTestProfilePath()
{
    return dirname(__DIR__).DIRECTORY_SEPARATOR.'Profiles/Dummy/';
}

/**
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 *
 * @param string $sProfile Profile name or path for loading specific profile.
 *
 * @return Gveniver\Kernel\Application
 */
function getApplication($sProfile = null)
{
    static $app;
    if (!$app)
        $app = new Gveniver\Kernel\Application($sProfile ? $sProfile : getTestProfilePath());
    return $app;
}