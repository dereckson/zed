<?php
/**
* Smarty plugin
*
* @package Smarty
* @subpackage PluginsModifier
*/

use Keruald\OmniTools\Culture\Rome\RomanNumerals;

/**
* Smarty romanize modifier plugin
*
* Type:     modifier<br>
* Name:     romanize<br>
* Purpose:  prints a number in roman
*
* @param int $
* @return string
*/
function smarty_modifier_romanize ($number) : string {
    try {
        return RomanNumerals::fromHinduArabic($number);
    } catch (Throwable $ex) {
        // Not a strictly positive integer, don't modify
        return $number;
    }
}
