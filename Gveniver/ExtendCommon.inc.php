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
// @codingStandardsIgnoreStart
function array_merge_recursive_distinct()
// @codingStandardsIgnoreEnd
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
// @codingStandardsIgnoreStart
function explode_ex($delimiter, $string)
// @codingStandardsIgnoreEnd
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
 * Function to strip tags and attributes, but with allowable attributes.
 * 
 * <code>
 * <?php strip_tags_ex($string,'<strong><em><a>','href,rel'); ?>
 * </code>
 * 
 * @param string $string          String to format.
 * @param string $allowtags       Accepatable HTML tags.
 * @param string $allowattributes Accepatable HTML attributes.
 * @param int    $nMaxLength      Maximal length of text.
 * @param string $sCropStr        Crop text (for mark crop point).
 * 
 * @return string
 */
// @codingStandardsIgnoreStart
function strip_tags_ex($string, $allowtags = null, $allowattributes = null, $nMaxLength = null, $sCropStr = '...')
// @codingStandardsIgnoreEnd
{
    $cTidy = new \Tidy();
    $cTidy->parseString(
        $string,
        array(
             'clean'          => true,
             'output-xml'     => true,
             'show-body-only' => true,
             'wrap' => 0
        ),
        'UTF8'
    );
    $cTidy->cleanRepair();

    $string = $cTidy->repairString(
        $string,
        array(
            'output-xhtml'    => true,
            'show-body-only'  => true,
            //'alt-text'        => '',
            //'drop-font-tags'  => true,
            'escape-cdata'    => true,
            'hide-comments'   => true,
            'join-styles'     => true,
            'join-classes'    => true,
            'quote-ampersand' => true,
            'quote-marks'     => true,
            'indent-spaces'   => 0,
            'newline'         => 'LF',
            'quote-nbsp'      => true,
            'quote-marks'     => true,
            'quote-ampersand' => true,
            'wrap'            => 0
        ),
        'UTF8'
    );

    $cDom = new \DomDocument();
    $string = str_replace('&nbsp;', '&#160;', $string);
    $string = str_replace('&quot;', '&#34;', $string);
    $string = str_replace('&gt;', '&#62;;', $string);
    $string = str_replace('&lt;', '&#60;', $string);
    $string = str_replace('&laquo;', '&#171;', $string);
    $string = str_replace('&raquo;', '&#187;', $string);

    // Build array of allowed attributes.
    $aAllowedAttriburtes = array();
    if ($allowattributes) {
        if (is_string($allowattributes))
            $aAllowedAttriburtes = explode(',', $allowattributes);
        elseif (is_array($allowattributes))
            $aAllowedAttriburtes = $allowattributes;
    }

    // Build array of allowed tags.
    $aAllowedTags = $allowtags ? explode(' ', $allowtags) : array();
    for ($i = 0; $i < count($aAllowedTags); $i++) {
        $aAllowedTags[$i] = str_replace('>', '', $aAllowedTags[$i]);
        $aAllowedTags[$i] = str_replace('<', '', $aAllowedTags[$i]);
        $aAllowedTags[$i] = trim($aAllowedTags[$i]);
    }

    //if (substr($string, 0, 1) != "<")
        $string = "<xml_main>$string</xml_main>";
    $cDom->loadXML($string);
    $cXml = $cDom->getElementsByTagName('xml_main')->item(0);

    // Function recursive walking over DOM tree and removing not allowed tags and attributes.
    $checkTree = function(\DOMNode &$cElement) use (&$aAllowedAttriburtes, &$aAllowedTags, &$checkTree) {

        $aNodesForDelete = array();
        foreach ($cElement->childNodes as $cNode) {

            /** @var $cNode \DOMElement */

            if ($cNode->nodeType != XML_ELEMENT_NODE)
                continue;

            // Remove not allowed tags.
            if (!in_array($cNode->tagName, $aAllowedTags)) {
                $aNodesForDelete[] = $cNode;
                continue;
            }

            // Remove not allowed attributes.
            foreach ($cNode->attributes as $cAttribute) {

                /** @var $cAttribute \DOMAttr */

                if (!in_array($cAttribute->name, $aAllowedAttriburtes))
                    $cNode->removeAttribute($cAttribute->name);
            }

            // Parse childs.
            $checkTree($cNode);
        }

        // Lazy deleting of marked nodes.
        foreach ($aNodesForDelete as $cNode)
            $cElement->removeChild($cNode);
    };
    $checkTree($cXml);

    // Check maximla length of text.
    if ($nMaxLength) {
        $nLength = 0;
        $bStop = false;
        $fCropText = function(\DOMNode &$cElement) use (&$nLength, &$fCropText, &$bStop, $sCropStr, $nMaxLength) {

            // Recursive walking over DOM tree, while length of text less then specified.
            // After exceeding the limit of length, deleting all other nodes.
            $aNodesForDelete = array();
            foreach ($cElement->childNodes as $cNode) {

                /** @var $cNode \DOMElement */

                if ($bStop) {
                    $aNodesForDelete[] = $cNode;
                    continue;
                }

                if ($cNode->nodeType == XML_TEXT_NODE) {
                    $nStartLength = $nLength;
                    $nLength += mb_strlen(trim($cNode->textContent));
                    if ($nLength > $nMaxLength) {
                        $bStop = true;
                        $cNode->nodeValue = str_break_text($cNode->nodeValue, $nMaxLength - $nStartLength, $sCropStr);
                    }
                }

                if ($cNode->nodeType == XML_ELEMENT_NODE)
                    $fCropText($cNode);
            }

            // Lazy deleting of marked nodes.
            foreach ($aNodesForDelete as $cNode)
                $cElement->removeChild($cNode);
        };
        $fCropText($cXml);

    } // End if
    
    $string = $cDom->saveXML($cXml);
    $string = substr($string, 10, $string - 11);
    return $string;

} // End function
//-----------------------------------------------------------------------------

/**
 * Extended version for array_shift to use with a-arrays.
 * 
 * @param array &$arr Array to shift.
 * 
 * @return array
 */
// @codingStandardsIgnoreStart
function array_shift_ex(&$arr)
// @codingStandardsIgnoreEnd
{
    list($k) = array_keys($arr);
    unset($arr[$k]);
    return $arr;

} // End function
//-----------------------------------------------------------------------------

/**
 * Function cuts string with simple text by the specified number of chars.
 *
 * @param string $string     Text to cut.
 * @param int    $nMaxLength Cutting length.
 * @param string $sCropStr   Text for appending (for mark crop point).
 *
 * @return string
 */
// @codingStandardsIgnoreStart
function str_break_text($string, $nMaxLength, $sCropStr = '...')
// @codingStandardsIgnoreEnd
{ 
    if (mb_strlen($string) > $nMaxLength) {
        $string = mb_substr($string, 0, $nMaxLength);
        $pos = mb_strrpos($string, ' '); 
        if ($pos === false) 
            return mb_substr($string, 0, $nMaxLength).$sCropStr;
            
        return mb_substr($string, 0, $pos).$sCropStr;
        
    } // End if
    
    return $string;
    
} // End function
//-------------------------------------------------------------------------------

/**
 * Format text for outputting.
 *
 * @param string $sText              Text to output.
 * @param int    $nMaxLength         Maximal length.
 *
 * @return string
 */
// @codingStandardsIgnoreStart
function output_text($sText, $nMaxLength = null)
// @codingStandardsIgnoreEnd
{
    if ($nMaxLength)
        $sText = str_break_text($sText, $nMaxLength);

    return strip_tags_ex($sText);

} // End function
//-------------------------------------------------------------------------------

/**
 * Format HTML content for outputting.
 *
 * @param string $sText              Text to output.
 * @param int    $nMaxLength         Maximal length.
 * @param string $sAllowedTags       List of allowed HTML tags.
 * @param string $sAllowedAttributes List of allowed HTML attributes.
 *
 * @return string
 */
// @codingStandardsIgnoreStart
function output_html($sText, $nMaxLength = null, $sAllowedTags = null, $sAllowedAttributes = null)
// @codingStandardsIgnoreEnd
{
    return strip_tags_ex($sText, $sAllowedTags, $sAllowedAttributes, $nMaxLength);

} // End function
//-------------------------------------------------------------------------------

/**
 * Check url correctness.
 *
 * @param string $sUrl Url to check.
 *
 * @return boolean True if correct.
 */
// @codingStandardsIgnoreStart
function is_correct_url($sUrl)
// @codingStandardsIgnoreEnd
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
 * @param $sFrom    Author of message.
 * @param $sTo      Recipient of message.
 * @param $sSubject Subject of message.
 * @param $sText    Text of message.
 *
 * @return bool True on success
 */
// @codingStandardsIgnoreStart
function mail($sFrom, $sTo, $sSubject, $sText)
// @codingStandardsIgnoreEnd
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
// @codingStandardsIgnoreStart
function realurl($sAddress)
// @codingStandardsIgnoreEnd
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