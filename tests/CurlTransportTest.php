<?php

namespace Cheddar;

use Cheddar\Transport\Curl;

class CurlTransportTest extends TestCase
{
    public function testDefaultOptions()
    {
        $get_request = new Curl('GET', 'https://httpbin.org/status/200');
        $this->assertEquals($get_request->httpStatusCode(), 200);

        $this->setExpectedException('Cheddar\Exceptions\ApiException');
        $invalid_request = new Curl('GET', 'xxx');
    }
}
