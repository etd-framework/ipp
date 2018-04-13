<?php
/**
 * Part of the ETD Framework IPP Package
 *
 * @copyright   Copyright (C) 208 ETD Solutions. Tous droits réservés.
 * @license     Apache License 2.0; see LICENSE
 * @author      ETD Solutions https://etd-solutions.com
 */

namespace EtdSolutions\IPP;

use EtdSolutions\ByteBuffer\ByteBuffer;
use Joomla\Http\HttpFactory;
use Joomla\Uri\Uri;

class Request {

    protected $adapter = 'curl';

    protected $response;

    /**
     * Request constructor.
     * @param Uri|string $url
     * @param ByteBuffer $buffer
     */
    public function __construct($url, $buffer) {

        $opts = [
            "transport.curl" => [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false
            ]
        ];

        //All IPP requires are POSTs- so we must have some data.
	    //  10 is just a number I picked- this probably should have something more meaningful
	    if (!ByteBuffer::isBuffer($buffer) || $buffer->length() < 10){
            throw new \RuntimeException("Data required");
        }

        if (is_string($url)) {
            $url = new Uri($url);
        }

        if ($url->getPort() === null) {
            $url->setPort(631);
        }

        $headers = ["Content-Type" => "application/ipp"];

        if ($url->getScheme() == 'ipp') {
            $url->setScheme('http');
        } elseif ($url->getScheme() == 'ipps') {
            $url->setScheme('https');
        }

        $http   = HttpFactory::getHttp($opts, $this->adapter);
        $parser = new Parser();

        $response = $http->post($url, $buffer->toString('binary'), $headers);
        if ($response->code != 200) {
            throw new \RuntimeException('Received unexpected response status ' . $response->code . ' from the printer.', $response->code);
        }

        $obj = $parser->parse(ByteBuffer::concat($response->body, strlen($response->body)));
        unset($obj["operation"]);

        $this->response = $obj;

    }

    public function getResponse() {
        return $this->response;
    }

}