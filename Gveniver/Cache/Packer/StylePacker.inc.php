<?php
/**
 * File contains class for packing CSS style data.
 * 
 * @category  Gveniver
 * @package   Cache
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

namespace Gveniver\Cache\Packer;

/**
 * Class for packing CSS style data.
 * 
 * @category  Gveniver
 * @package   Cache
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class StylePacker extends BaseDataPacker
{
    /**
     * Method for packing data.
     * Pack CSS style content.
     *
     * @param string $sData Data for packing.
     *
     * @return string
     */
    public function pack($sData)
    {
        $sData = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $sData);
        $sData = preg_replace('[^a-zA-Z0-9,:;]', '', $sData);
        $sData = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $sData);
        return $sData."\n";

    } // End function
    //--------------------------------------------------------------------------------

} // End class
//--------------------------------------------------------------------------------