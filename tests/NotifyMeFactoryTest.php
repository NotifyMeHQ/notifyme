<?php

/*
 * This file is part of NotifyMe.
 *
 * (c) Joseph Cohen <joseph.cohen@dinkbit.com>
 * (c) Graham Campbell <graham@mineuk.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use NotifyMeHQ\NotifyMe\NotifyMeFactory;

class NotifyMeFactoryTest extends PHPUnit_Framework_TestCase
{
    protected $factory;

    protected function setUp()
    {
        $this->factory = new NotifyMeFactory();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage A driver must be specified.
     */
    public function testNoDriverSpecified()
    {
        $this->factory->make([]);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Unsupported factory [foo].
     */
    public function testNoSupportedDriverSpecified()
    {
        $this->factory->make(['driver' => 'foo']);
    }

    /**
     * @expectedExceptionMessage Unsupported factory [foo].
     */
    public function testNoSupportedFactorySpecified()
    {
        $this->factory->factory('bar');
    }
}
