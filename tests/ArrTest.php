<?php

/*
 * This file is part of NotifyMe.
 *
 * (c) Cachet HQ <support@cachethq.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use NotifyMeHQ\NotifyMe\Arr;

class ArrTest extends PHPUnit_Framework_TestCase
{
    public function testArrGet()
    {
        $array = [
            'foo' => 'bar',
        ];

        $a1 = Arr::get($array, 'foo');
        $a2 = Arr::get($array, 'baz', 'default');

        $this->assertEquals('bar', $a1);
        $this->assertEquals('default', $a2);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testArrRequires()
    {
        $array = [
            'foo' => 'bar',
        ];

        Arr::requires($array, ['baz']);
    }
}
