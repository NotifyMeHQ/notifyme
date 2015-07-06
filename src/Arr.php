<?php

/*
 * This file is part of NotifyMe.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NotifyMeHQ\NotifyMe;

use InvalidArgumentException;

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

    /**
     * Check the array contains the required keys.
     *
     * @param string[] $options
     * @param string[] $required
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    public static function requires(array $options, array $required = [])
    {
        foreach ($required as $key) {
            if (!array_key_exists(trim($key), $options)) {
                throw new InvalidArgumentException("Missing required parameter: {$key}");
            }
        }
    }
}
