<?php
declare(strict_types=1);

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
    if (!is_numeric($number)) {
        return (string)$number;
    }

    try {
        return RomanNumerals::fromHinduArabic((int)$number);
    } catch (InvalidArgumentException $ex) {
        // Not a strictly positive integer, don't modify
        return (string)$number;
    }
}
