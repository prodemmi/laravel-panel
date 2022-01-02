<?php

namespace Prodemmi\Lava;


class ActionStatus
{

    public static function info($message): array
    {
        return [
            'type'    => __FUNCTION__,
            'message' => $message
        ];
    }

    public static function error($message): array
    {
        return [
            'type'    => __FUNCTION__,
            'message' => $message
        ];
    }

    public static function success($message): array
    {
        return [
            'type'    => __FUNCTION__,
            'message' => $message
        ];
    }

    public static function alert($message): array
    {
        return [
            'type'    => __FUNCTION__,
            'message' => $message
        ];
    }

    public static function dialog($title, $view): array
    {
        return [
            'type'  => __FUNCTION__,
            'title' => $title,
            'view'  => $view
        ];
    }

    public static function newWindow($url, $blank = TRUE): array
    {
        return [
            'type'  => __FUNCTION__,
            'url'   => $url,
            'blank' => $blank
        ];
    }

    public static function route($name, $params): array
    {
        return [
            'type'    => __FUNCTION__,
            'name'    => $name,
            'params' => $params
        ];
    }

}