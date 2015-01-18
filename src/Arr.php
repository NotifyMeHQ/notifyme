<?php

namespace NotifyMeHQ\NotifyMe;

class Arr
{
    /**
     * Get an item from an array.
     *
     * @param array  $array
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public static function get(&$array, $key, $default = null)
    {
        return isset($array[$key]) ? $array[$key] : $default;
    }
}
