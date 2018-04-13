<?php

    include_once ("../vendor/autoload.php");

    $printer  = new \EtdSolutions\IPP\Printer("ipp://cp02.local./ipp/printer");
    $msg = [
        "operation-attributes-tag" => [
            //	"limit" => 10,
            "which-jobs" => "completed",
		    "requested-attributes" => [
                "job-id",
                "job-uri",
                "job-state",
                "job-state-reasons",
                "job-name",
                "job-originating-user-name",
                "job-media-sheets-completed"
            ]
        ]
    ];

    //use these to view completed jobs...
    $response = $printer->execute($msg, "Get-Jobs");

    print_r($response);