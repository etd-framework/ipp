<?php

    include_once ("../vendor/autoload.php");

    $printer  = new \EtdSolutions\IPP\Printer("https://cp02.local./ipp/printer");
    $msg = [
        "operation-attributes-tag" => [
            "requesting-user-name" => "ETD Solutions",
            "job-name" => "test.pdf",
            "document-format" => "application/pdf"
        ],
        "job-attributes-tag" => [
            "copies" => 2
        ],
        "data" => \EtdSolutions\ByteBuffer\ByteBuffer::from(file_get_contents("./test.pdf"), 'binary')
    ];

    $response = $printer->execute($msg, "Print-Jobs");

    print_r($response);