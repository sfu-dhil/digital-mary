<?php


namespace App\Util;

class Initializer {

    public static function generate(string $s) : string {
        if( ! $s) {
            return '';
        }
        $words = preg_split('/\s+/u', $s);
        if(count($words) === 1) {
            return mb_convert_case(mb_substr($words[0], 0,1) . $words[0][1], MB_CASE_UPPER);
        }
        return mb_convert_case($words[0][0] . end($words)[0], MB_CASE_UPPER);
    }

}
