<?php

    include_once ("../vendor/autoload.php");

    $printer  = new \EtdSolutions\IPP\Printer("http://cp02.local.:631/ipp/printer");
    $msg = [
        "operation-attributes-tag" => [
            "document-format" => "application/pdf",
		    "requested-attributes" => [
                "queued-job-count",
                "marker-levels",
                "printer-state",
                "printer-state-reasons",
                "printer-up-time"
            ]
        ]
    ];
    $response = $printer->execute($msg, "Get-Printer-Attributes");

    print_r($response);