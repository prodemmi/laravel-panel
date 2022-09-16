<?php

namespace Prodemmi\Lava\Utils;

class LavaHelper
{

    public static function arr_group_by(array $data, $key )
    {
        $result = [];

        foreach ( $data as $val ) {
            if ( array_key_exists( $key, $val ) ) {
                $result[$val[$key]][] = $val;
            }
            else {
                $result["no_group"][] = $val;
            }
        }

        return $result;
    }

    public static function str_sanitize($filename, $force_lowercase = true, $anal = false) {
        $strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]",
                       "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
                       "â€”", "â€“", ",", "<", ".", ">", "/", "?");
        $clean = trim(str_replace($strip, "", strip_tags($filename)));
        $clean = preg_replace('/\s+/', "-", $clean);
        $clean = ($anal) ? preg_replace("/[^a-zA-Z0-9]/", "", $clean) : $clean ;
        return ($force_lowercase) ?
            (function_exists('mb_strtolower')) ?
                mb_strtolower($clean, 'UTF-8') :
                strtolower($clean) :
            $clean;
    }

}