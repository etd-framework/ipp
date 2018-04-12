<?php

    echo str_repeat("\n", 20);
    echo "________________________________________________________________________________________________________________________________________";
    echo str_repeat("\n", 20);

    require_once(realpath(__DIR__ . "/../vendor/autoload.php"));

    $printer = new EtdSolutions\IPP\Printer("ipp://82.127.15.108:10632/ipp/print");

    $msg = [
        "operation-attributes-tag" => [
            "requesting-user-name" => "webquin",
			"job-name"             => "My Test Job",
			"document-format"      => "text/plain"
		],
		"data" => \EtdSolutions\IPP\Buffer::from("c'est un test !", "utf8")
    ];
    $response = $printer->execute($msg, "Print-Job");

    print_r($response);