<?php

namespace Prodemmi\Lava\Utils;

class ArrayHelper
{

    public static function group_by(array $data, $key )
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

}