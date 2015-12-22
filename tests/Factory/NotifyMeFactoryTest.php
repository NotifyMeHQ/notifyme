<?php

/*
 * This file is part of NotifyMe.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NotifyMeHQ\Tests\Factory;

use NotifyMeHQ\Factory\NotifyMeFactory;
use PHPUnit_Framework_TestCase;

class NotifyMeFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage A driver must be specified.
     */
    public function testNoDriverSpecified()
    {
        $factory = new NotifyMeFactory();

        $factory->make([]);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Unsupported factory [foo].
     */
    public function testNoSupportedDriverSpecified()
    {
        $factory = new NotifyMeFactory();

        $factory->make(['driver' => 'foo']);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Unsupported factory [bar].
     */
    public function testNoSupportedFactorySpecified()
    {
        $factory = new NotifyMeFactory();

        $factory->factory('bar');
    }
}
