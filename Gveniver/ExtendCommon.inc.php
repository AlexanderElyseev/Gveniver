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
            if (!array_key_exists($key, $base) and !is_numeric($key)) {
                $base[$key] = $append[$key];
                continue;
            } // End if
            if (is_array($value) || is_array($base[$key])) {
                if (is_numeric($key))
                    $base[] = array_merge_recursive_distinct(array(), $append[$key]);
                else
                    $base[$key] = array_merge_recursive_distinct($base[$key], $append[$key]);
            } else if (is_numeric($key)) {
                if (!in_array($value, $base)) $base[] = $value;
            } else {
                $base[$key] = $value;
            } // End else
        } // End foreach
    } // End foreach
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
 * @param string $string          Строка для форматирования.
 * @param string $allowtags       Строка разрешенных тегов.
 * @param string $allowattributes Строка разрешенных аттрибутов (через запятую).
 * 
 * @return string
 */
// @codingStandardsIgnoreStart
function strip_tags_ex($string, $allowtags = null, $allowattributes = null)
// @codingStandardsIgnoreEnd
{
    $tidy_config = array(
         'clean'          => true,
         'output-xml'     => true,
         'show-body-only' => true,
         'wrap' => 0,

    );

    $cTidy = new \Tidy();
    $cTidy ->parseString($string, $tidy_config, 'UTF8');
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
            'break-before-br' => true,
            'indent'          => 'auto',
            'newline'         => 'LF',
            'quote-nbsp'      => true,
            'quote-marks'     => true,
            'quote-ampersand' => true
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

    //if (substr($string, 0, 1) != "<")
        $string = "<xml_main>".$string."</xml_main>";

    $cDom->loadXML($string);

    if ($allowattributes) {
        if (!is_array($allowattributes))
            $allowattributes = explode(",", $allowattributes);
    }

    if ($allowtags) {
        $allowtags = explode(" ", $allowtags);
        for ($i = 0; $i < count($allowtags); $i++) {
            $allowtags[$i] = trim(str_replace(">", "", str_replace("<", "", $allowtags[$i])));
        }
    }

    $cXml = $cDom->getElementsByTagName('xml_main')->item(0);
    $strip_tag = function(\DOMNode &$cElement, $allowattributes, $allowtags, $strip_tag) {
        foreach ($cElement->childNodes as $cNode) {

            /** @var $cNode \DOMElement */

            if ($cNode->nodeType != XML_ELEMENT_NODE)
                continue;

            if ($allowtags) {
                if (!in_array($cNode->tagName, $allowtags))
                    $cElement->removeChild($cNode);
            }

            if ($allowattributes) {
                foreach ($cNode->attributes as $cAttribute) {

                    /** @var $cAttribute \DOMAttr */

                    if (!in_array($cAttribute->name, $allowattributes))
                        $cNode->removeAttribute($cAttribute->name);
                }
            }

            $strip_tag($cNode, $allowattributes, $allowtags, $strip_tag);
        }
    };
    $strip_tag($cXml, $allowattributes, $allowtags, $strip_tag);
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
 * Альтернативный метод перевода строки в верхний регистр при проблеме
 * с русским языком в строке.
 * 
 * @param string $str Строка для преобразования.
 * 
 * @return string
 */
// @codingStandardsIgnoreStart
function strtolower_ex($str)
// @codingStandardsIgnoreEnd
{
    $aAlphaLower = array(
        'ё', 'й', 'ц', 'у', 'к', 'е', 'н', 'г', 'ш', 'щ', 'з', 'х', 'ъ', 
        'ф', 'ы', 'в', 'а', 'п', 'р', 'о', 'л', 'д', 'ж', 'э', 'я', 'ч',
        'с', 'м', 'и', 'т', 'ь', 'б', 'ю'
    );

    $aAlphaUpper = array(
        'Ё', 'Й', 'Ц', 'У', 'К', 'Е', 'Н', 'Г', 'Ш', 'Щ', 'З', 'Х', 'Ъ',
        'Ф', 'Ы', 'В', 'А', 'П', 'Р', 'О', 'Л', 'Д', 'Ж', 'Э', 'Я', 'Ч',
        'С', 'М', 'И', 'Т', 'Ь', 'Б', 'Ю'
    );
    
    return str_replace($aAlphaUpper, $aAlphaLower, strtolower($str));
    
} // End function
//-------------------------------------------------------------------------------

/**
 * Альтернативный метод перевода строки в нижний регистр при проблеме
 * с русским языком в строке.
 * 
 * @param string $str Строка для преобразования.
 * 
 * @return string
 */
// @codingStandardsIgnoreStart
function strtoupper_ex($str)
// @codingStandardsIgnoreEnd
{
    $aAlphaLower = array(
        'ё', 'й', 'ц', 'у', 'к', 'е', 'н', 'г', 'ш', 'щ', 'з', 'х', 'ъ', 
        'ф', 'ы', 'в', 'а', 'п', 'р', 'о', 'л', 'д', 'ж', 'э', 'я', 'ч',
        'с', 'м', 'и', 'т', 'ь', 'б', 'ю'
    );

    $aAlphaUpper = array(
        'Ё', 'Й', 'Ц', 'У', 'К', 'Е', 'Н', 'Г', 'Ш', 'Щ', 'З', 'Х', 'Ъ',
        'Ф', 'Ы', 'В', 'А', 'П', 'Р', 'О', 'Л', 'Д', 'Ж', 'Э', 'Я', 'Ч',
        'С', 'М', 'И', 'Т', 'Ь', 'Б', 'Ю'
    );
    
    return str_replace($aAlphaLower, $aAlphaUpper, strtoupper($str));
    
} // End function
//-------------------------------------------------------------------------------

/**
 * Function cuts string with simple text by the specified number of chars.
 *
 * @param string $string     Text to cut.
 * @param int    $max_length Cutting length.
 * 
 * @return string
 */
// @codingStandardsIgnoreStart
function str_break_text($string, $max_length)
// @codingStandardsIgnoreEnd
{ 
    if (mb_strlen($string) > $max_length) { 
        $string = mb_substr($string, 0, $max_length); 
        $pos = mb_strrpos($string, ' '); 
        if ($pos === false) 
            return mb_substr($string, 0, $max_length)."..."; 
            
        return mb_substr($string, 0, $pos).'...'; 
        
    } // End if
    else
        return $string; 
    
} // End function
//-------------------------------------------------------------------------------

/**
 * Function cuts string with the HTML tags by the specified number of chars and strips 
 * empty HTML tags from the output.
 *
 * @param string $txt   text to cut
 * @param int    $len   number of chars to keep in the resulting string
 * @param string $delim optional string of the stop-chars, used to split the text when 
 * limit reached in the middle of the current word
 * 
 * @return string
 * @author Ilya Lebedev
 */
// @codingStandardsIgnoreStart
function str_break_html($txt, $len, $delim = '\s;,.!?:#')
// @codingStandardsIgnoreEnd
{
    $txt = preg_replace_callback(
        "#(</?[a-z]+(?:>|\s[^>]*>)|[^<]+)#mi",    // TODO mb_
        create_function(
            '$a',
            'static $len = '.$len.';'
            .'$len1 = $len-1;'
            .'$delim = \''.str_replace("#", "\\#", $delim).'\';'
            .'if ("<" == $a[0]{0}) return $a[0];'
            .'if ($len<=0) return "";'
            .'$res = preg_split("#(.{0,$len1}+(?=[$delim]))|(.{0,$len}[^$delim]*)#ms",$a[0],2,PREG_SPLIT_DELIM_CAPTURE);'
            .'if ($res[1]) { $len -= strlen($res[1])+1; $res = $res[1];}'
            .'else         { $len -= strlen($res[2]); $res = $res[2];}'
            .'$res = rtrim($res);/*preg_replace("#[$delim]+$#m","",$res);*/'
            .'return $res;'
        ),
        $txt
    );
                                  
     while (preg_match("#<([a-z]+)[^>]*>\s*</\\1>#mi", $txt))
         $txt = preg_replace("#<([a-z]+)[^>]*>\s*</\\1>#mi", "", $txt);
     
     return $txt;
     
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