<?php

    require_once(realpath(__DIR__ . "/../vendor/autoload.php"));

    $printer = new EtdSolutions\IPP\Printer("ipps://82.127.15.108:10632/ipp/printer");

    $response = $printer->execute([], "Get-Printer-Attributes");

    print_r($response);