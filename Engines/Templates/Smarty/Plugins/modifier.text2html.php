<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty text to HTML modifier plugin
 *
 * Type:     modifier<br>
 * Name:     text2html<br>
 * Purpose:  HTML representation for Azhàr profiles
 * @author   Wolfaeym
 * @param string
 * @return string
 */
function smarty_modifier_text2html ($string) {
    //HTML entities
    $string = htmlspecialchars($string, ENT_QUOTES);

    //E-mail -> mailto links
    $string = preg_replace(
        "/([_a-z0-9-]+(\._a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)+)/i",
        "<a href=\"mailto:\\1\">\\1</A>",
        $string
    );

    //Linkify URLs
    //TODO handle www.
    //TODO Relative links for current domain
    $string = preg_replace(
        "@([[:alnum:]]+)://([^[:space:]]*)([[:alnum:]#?/&=])@i",
        "<a href=\"\\1://\\2\\3\">\\1://\\2\\3</a>", $string
    );

    //\n -> <br />
    $string = nl2br($string);

    return $string;
}
