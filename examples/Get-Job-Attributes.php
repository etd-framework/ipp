<?php

    include_once ("../vendor/autoload.php");

    $printer  = new \EtdSolutions\IPP\Printer("http://cp02.local.:631/ipp/printer");
    $msg = [
        "operation-attributes-tag" => [
            "job-uri" => "ipp://CP01.local/ipp/printer/0186"
        ]
    ];

    // Polling for job status
    while (true) {

        $response = $printer->execute($msg, "Get-Job-Attributes");
        print_r($response);

        sleep(1);
    }