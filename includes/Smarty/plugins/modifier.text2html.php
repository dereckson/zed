<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty lower modifier plugin
 *
 * Type:     modifier<br>
 * Name:     yyyymmdd<br>
 * Purpose:  Appends dashes to yyyymmdd string to output yyyy-mm-dd
 * @author   Wolfaeym
 * @param string
 * @return string
 */
function smarty_modifier_text2html($string)
{
    //HTML entities
    $string = htmlspecialchars($string, ENT_QUOTES);
    
    //E-mail -> mailto links
    $string = eregi_replace("([_a-z0-9-]+(\._a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)+)", "<a href=\"mailto:\\1\">\\1</A>", $string);

    //Linkify URLs
    //TODO handle www.
    //TODO Relative links for current domain
    $string = eregi_replace("([[:alnum:]]+)://([^[:space:]]*)([[:alnum:]#?/&=])", "<a href=\"\\1://\\2\\3\">\\1://\\2\\3</a>", $string);
    
    //\n -> <br />
    $string = nl2br($string);
    
    return $string;
}

?>
