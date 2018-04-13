<?php

    include_once ("../vendor/autoload.php");

    $printer  = new \EtdSolutions\IPP\Printer("ipp://cp02.local.:631/ipp/printer");
    $response = $printer->execute([], "Get-Printer-Attributes");

    print_r($response);