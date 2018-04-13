<?php
/**
 * Part of the ETD Framework IPP Package
 *
 * @copyright   Copyright (C) 208 ETD Solutions. Tous droits réservés.
 * @license     Apache License 2.0; see LICENSE
 * @author      ETD Solutions https://etd-solutions.com
 */

namespace EtdSolutions\IPP;

use Joomla\Uri\Uri;

class Printer {

    /**
     * @var Uri
     */
    protected $url;

    /**
     * @var string
     */
    protected $version = '2.0';

    /**
     * @var string
     */
    protected $uri;

    /**
     * @var string
     */
    protected $charset = 'utf-8';

    /**
     * @var string
     */
    protected $language = 'en-us';

    /**
     * Printer constructor.
     *
     * @param string|Uri $url
     * @param array      $options
     */
    public function __construct($url, $options = []) {

        $this->url = is_string($url) ? new Uri($url) : $url;

        if (isset($options['version'])) {
            $this->version = $options['version'];
        }

        if (isset($options['uri'])) {
            $this->uri = $options['uri'];
        } else {
            $this->uri = 'ipp://' . $this->url->getHost() . $this->url->getPath();
        }

        if (isset($options['charset'])) {
            $this->version = $options['charset'];
        }

        if (isset($options['language'])) {
            $this->version = $options['language'];
        }

    }

    public function execute($msg = [], $operation = 'Get-Printer-Attributes') {

        $serializer = new Serializer();
        $msg        = $this->message($msg, $operation);
        $buffer     = $serializer->serialize($msg);

        $request = new Request($this->url, $buffer);
        return $request->getResponse();

    }

    protected function message($msg = [], $operation = 'Get-Printer-Attributes') {

        $base = [
            "version"                  => $this->version,
            "operation"                => $operation,
            "id"                       => null, // will get added by serializer if one isn't given
            "operation-attributes-tag" => [
                // these are required to be in this order
                "attributes-charset"          => $this->charset,
                "attributes-natural-language" => $this->language,
                "printer-uri"                 => $this->uri
            ]
        ];

        // these are required to be in this order
        if (isset($msg) && isset($msg["operation-attributes-tag"]["job-id"])) {
            $base["operation-attributes-tag"]["job-id"] = $msg["operation-attributes-tag"]["job-id"];
        } elseif (isset($msg) && isset($msg["operation-attributes-tag"]["job-uri"])) {
            $base["operation-attributes-tag"]["job-uri"] = $msg["operation-attributes-tag"]["job-uri"];
        }

        $msg = array_replace_recursive($base, $msg);

        if (isset($msg["operation-attributes-tag"]["job-uri"])) {
            unset($msg["operation-attributes-tag"]["printer-uri"]);
        }

        return $msg;

    }

}