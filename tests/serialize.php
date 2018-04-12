<?php

    echo str_repeat("\n", 20);
    echo "________________________________________________________________________________________________________________________________________";
    echo str_repeat("\n", 20);

    require_once(realpath(__DIR__ . "/../vendor/autoload.php"));

    $data = [
        "operation"                => "Get-Printer-Attributes",
        "operation-attributes-tag" => [
            "attributes-natural-language" => "en-us",
            "printer-uri"                 => "ipp://82.127.15.108/ipp/printer",
            "attributes-charset"          => "utf-8"
        ]
    ];

    print_r($data);

    $serializer = new EtdSolutions\IPP\Serializer();
    $result = $serializer->serialize($data);
    echo "\n\n";
    echo "hex : "  . $result->toString('hex') . "\n";

    $parser = new EtdSolutions\IPP\Parser();
    $result = $parser->parse($result);
    print_r($result);