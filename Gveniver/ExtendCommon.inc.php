<?php
/**
 * File contains extended versions on various PHP functions.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

namespace Gveniver;

/**
 * Recursive merging of arrays.
 * If key for merge is numeric, element append to list, no over write.
 *
 * @return array|mixed
 */
function array_merge_recursive_distinct()
{
    $arrays = func_get_args();
    $base = array_shift($arrays);
    if (!is_array($base))
        $base = empty($base) ? array() : array($base);
    foreach ($arrays as $append) {
        if (!is_array($append))
            $append = array($append);
        foreach ($append as $key => $value) {
            if (!array_key_exists($key, $base) && !is_numeric($key)) {
                $base[$key] = $append[$key];
                continue;
            }
            if (is_array($value) || is_array($base[$key])) {
                if (is_numeric($key))
                    $base[] = array_merge_recursive_distinct(array(), $append[$key]);
                else
                    $base[$key] = array_merge_recursive_distinct($base[$key], $append[$key]);
            } else if (is_numeric($key)) {
                if (!in_array($value, $base))
                    $base[] = $value;
                else
                    $base[$key] = $value;
            } else {
                $base[$key] = $value;
            }
        }
    }
    return $base;
    
} // End function
//-----------------------------------------------------------------------------

/**
 * Analogue of standard PHP function with support of escaped delimiters.
 *
 * <code>
 * <?php
 *        $result = explode_ex(',', 'string, piece, group\, item\, item2, next\,asd');
 *        print_r($result);
 *        ?>
 *        Will give:
 *        Array
 *        (
 *            [0] => string
 *            [1] => piece
 *            [2] => group, item, item2
 *            [3] => next,asd
 *        )
 * </code>
 * 
 * @param string $delimiter Delimiter for exploding.
 * @param string $string    String for analyze.
 * 
 * @return array
 */
function explode_ex($delimiter, $string)
{
    $exploded = explode($delimiter, $string);
    $fixed = array();
    for ($k = 0, $l = count($exploded); $k < $l; ++$k) {
        if (isset($exploded[$k][strlen($exploded[$k]) - 1]) && $exploded[$k][strlen($exploded[$k]) - 1] == '\\') {
            if ($k + 1 >= $l) {
                $fixed[] = trim($exploded[$k]);
                break;
            }
            $exploded[$k][strlen($exploded[$k]) - 1] = $delimiter;
            $exploded[$k] .= $exploded[$k + 1];
            array_splice($exploded, $k + 1, 1);
            --$l; 
            --$k;
        } else $fixed[] = trim($exploded[$k]);
    }
    return $fixed;

} // End function
//-----------------------------------------------------------------------------

/**
 * Extended version for array_shift to use with a-arrays.
 * 
 * @param array &$arr Array to shift.
 * 
 * @return array
 */
function array_shift_ex(&$arr)
{
    list($k) = array_keys($arr);
    unset($arr[$k]);
    return $arr;

} // End function
//-----------------------------------------------------------------------------

/**
 * Check url correctness.
 *
 * @param string $sUrl Url to check.
 *
 * @return boolean True if correct.
 */
function is_correct_url($sUrl)
{
    $sPattern = '{
        (?:
        (\w+://)
        |
        www\.
        )
        [\w-]+(\.[\w-]+)*
        \S*
        (?:
        (?<! [[:punct:]])
        | (?<= [-/&+*])
        )
    }xis';

    return preg_match($sPattern, $sUrl);

} // End function
//-----------------------------------------------------------------------------

/**
 * Replace special symbols for XML CDATA.
 *
 * @param string $sContent Text for replacing.
 *
 * @return string
 */
function cdata($sContent)
{
    return '<![CDATA['.str_replace(']]>', ']]]]><![CDATA[>', $sContent).']]>';

} // End function
//-----------------------------------------------------------------------------

/**
 * Send the mail.
 *
 * @param string $sFrom    Author of message.
 * @param string $sTo      Recipient of message.
 * @param string $sSubject Subject of message.
 * @param string $sText    Text of message.
 *
 * @return bool True on success
 */
function mail($sFrom, $sTo, $sSubject, $sText)
{
    // Build e-mail headers.
    $sHeaders = sprintf("From: %s\r\n", $sFrom);
    $sHeaders .= "MIME-Version: 1.0\r\n";
    $sHeaders .= "Content-Type: text/plain; charset=\"UTF-8\"\r\n";

    // Standart sending.
    // TODO: check address.
    return \mail(
        $sTo,
        $sSubject,
        $sText,
        $sHeaders
    );

} // End function
//-----------------------------------------------------------------------------

/**
 * Function to canonicalize a URL containing relative paths.
 *
 * @param string $sAddress Address for analyzing.
 *
 * @return string
 */
function realurl($sAddress)
{
    $address = explode('/', $sAddress);
    $keys = array_keys($address, '..');

    foreach($keys AS $keypos => $key)
        array_splice($address, $key - ($keypos * 2 + 1), 2);

    $address = implode('/', $address);
    return str_replace('./', '', $address);

} // End function
//-----------------------------------------------------------------------------

/**
 * Check, is this path is absolute.
 *
 * @param string $sPath Path to check.
 *
 * @return bool True if absolute.
 */
function isAbsolutePath($sPath)
{
    if (defined('GV_OS_WIN') && GV_OS_WIN)
        return preg_match('/^[a-z]:/i', $sPath);

    return isset($sPath[0]) && $sPath[0] === '/';

} // End function
//-----------------------------------------------------------------------------

/**
 * Correction method of the directory separator character.
 * Replace all wrong characters to correct directory separator.
 *
 * @param string $sFileName        Name of file.
 * @param bool   $bAddDirSeparator Is need to append directory separator to end of path.
 *
 * @return string
 */
function correctPath($sFileName, $bAddDirSeparator = false)
{
    $sFileName = (GV_DS === '/')
        ? str_replace('\\', GV_DS, $sFileName)
        : str_replace('/', GV_DS, $sFileName);

    if (!isAbsolutePath($sFileName))
        $sFileName = GV_PATH_BASE.$sFileName;

    $sRealFileName = realpath($sFileName);
    $sFileName =  $sRealFileName ? $sRealFileName : $sFileName;

    if (($bAddDirSeparator || is_dir($sFileName)) && substr($sFileName, -1) != GV_DS)
        $sFileName = $sFileName.GV_DS;

    return $sFileName;

} // End function
//-----------------------------------------------------------------------------

/**
 * Method deletes specified directory with all subdirectories and files.
 *
 * @param string $sDirectoryPath Path to directory for removing.
 *
 * @return boolean
 */
function rrmdir($sDirectoryPath)
{
    if (!file_exists($sDirectoryPath) || !is_dir($sDirectoryPath))
        return false;

    $bResult = true;
    foreach (glob($sDirectoryPath . '/*') as $sFileName) {
        if (is_dir($sFileName))
            $bResult = $bResult && rrmdir($sFileName);
        else
            $bResult = $bResult && unlink($sFileName);

        if (!$bResult)
            break;
    }
    return $bResult && rmdir($sDirectoryPath);

} // End function
//-----------------------------------------------------------------------------

/**
 * Converts value to boolean.
 *
 * @param mixed $mValue The value to convert.
 *
 * @return bool Convert result.
 */
function toBoolean($mValue)
{
    if ($mValue === true || $mValue === 1 || $mValue === '1' || $mValue === 'true')
        return true;

    return false;

} // End function
//-----------------------------------------------------------------------------

/**
 * Convert specified value to integer, if value is not null.
 *
 * @param mixed $mValue The value to convert.
 *
 * @return integer|null Converted result.
 */
function toIntegerOrNull($mValue)
{
    return is_null($mValue) ? null : intval($mValue);

} // End function
//-----------------------------------------------------------------------------