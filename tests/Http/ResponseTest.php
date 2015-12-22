<?php

/*
 * This file is part of NotifyMe.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NotifyMeHQ\Tests\Http;

use NotifyMeHQ\Http\Response;
use PHPUnit_Framework_TestCase;

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
