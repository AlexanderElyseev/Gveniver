<?php
/**
 * Builder to PHAR archive.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
$p = new Phar('gveniver.phar');
$p->buildFromDirectory(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'gveniver');
if (Phar::canCompress(Phar::GZ)) {
    $p->compress(Phar::GZ, '.phar.gz');
} elseif (Phar::canCompress(Phar::BZ2)) {
    $p->compress(Phar::BZ2, '.phar.bz2');
}