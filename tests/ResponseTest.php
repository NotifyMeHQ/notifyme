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

use NotifyMeHQ\NotifyMe\Response;

class ResponseTest extends PHPUnit_Framework_TestCase
{
    public function testMapArrayToResponse()
    {
        $raw = [
            'foo' => 'bar',
        ];

        $response = new Response();
        $response->map($raw);

        $this->assertEquals($response->foo, 'bar');
    }

    public function testSetRawData()
    {
        $raw = [
            'foo' => 'bar',
        ];

        $response = new Response();
        $response->setRaw($raw);

        $this->assertArrayHasKey('foo', $response->raw());
    }

    public function testResponseStatus()
    {
        $response = new Response();

        $response->map([
            'success' => true,
        ]);

        $this->assertTrue($response->isSent());

        $response->map([
            'success' => false,
        ]);

        $this->assertFalse($response->isSent());
    }

    public function testResponseMessage()
    {
        $response = new Response();

        $response->map([
            'message' => 'foo',
        ]);

        $this->assertEquals($response->message(), 'foo');
    }
}
