<?php
/**
* Smarty plugin
*
* @package Smarty
* @subpackage PluginsModifier
*/

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
    return romanize($number);
}

function romanize ($number) : string {
    switch ($number) {
        case 1:
            return 'i';
        case 2:
            return 'ii';
        case 3:
            return 'iii';
        case 4:
            return 'iv';
        case 5:
            return 'v';
        case 6:
            return 'vi';
        case 7:
            return 'vii';
        case 8:
            return 'viii';
        case 9:
            return 'ix';
        case 10:
            return 'x';

        case 50:
            return 'l';
        case 100:
            return 'c';
        case 500:
            return 'd';
        case 1000:
            return 'm';

        default:
            if ($number < 21) {
                return 'x' . romanize($number - 10);
            } elseif ($number < 30) {
                return 'xx' . romanize($number - 20);
            } elseif ($number < 40) {
                return 'xxx' . romanize($number - 30);
            } elseif ($number < 50) {
                return 'xl' . romanize($number - 40);
            } elseif ($number < 60) {
                return 'l' . romanize($number - 50);
            } elseif ($number < 70) {
                return 'lx' . romanize($number - 60);
            } elseif ($number < 80) {
                return 'lxx' . romanize($number - 70);
            } elseif ($number < 90) {
                return 'lxxx' . romanize($number - 80);
            } elseif ($number < 100) {
                return 'xc' . romanize($number - 90);
            } elseif ($number < 200) {
                return 'c' . romanize($number - 100);
            } elseif ($number < 300) {
                return 'cc' . romanize($number - 200);
            } elseif ($number < 400) {
                return 'ccc' . romanize($number - 300);
            } elseif ($number < 500) {
                return 'cd' . romanize($number - 400);
            } elseif ($number < 600) {
                return 'd' . romanize($number - 500);
            } elseif ($number < 700) {
                return 'dc' . romanize($number - 600);
            } elseif ($number < 800) {
                return 'dcc' . romanize($number - 700);
            } elseif ($number < 900) {
                return 'dccc' . romanize($number - 800);
            } elseif ($number < 1000) {
                return 'cm' . romanize($number - 800);
            } elseif (is_numeric($number)) {
                $m = floor($number / 1000);

                return str_repeat('m', $m) . romanize($number - $m);
            }

            // Not a arab number, return as is
            return $number;
    }
}
