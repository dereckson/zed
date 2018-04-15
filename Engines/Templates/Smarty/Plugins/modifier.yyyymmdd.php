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
function smarty_modifier_yyyymmdd($string) {
    return substr($string, 0, 4) . '-' . substr($string, 4, 2)
           . '-' . substr($string, 6, 2);
}
