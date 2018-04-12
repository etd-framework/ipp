<?php

    require_once(realpath(__DIR__ . "/../vendor/autoload.php"));

    $hex = hex2bin(
        '0200' .	//version 2.0
        '000B' .	    //Get-Printer-Attributes
        '00000003' .	//reqid
        '01' .		    //operation-attributes-tag
        //blah blah the required bloat of this protocol
        '470012617474726962757465732d6368617273657400057574662d3848001b617474726962757465732d6e61747572616c2d6c616e67756167650002656e' .
        '03'		    //end-of-attributes-tag
    );

    echo "bin : $hex\n";
    $buffer = EtdSolutions\IPP\Buffer::from($hex, 'binary');
    echo "hex : "  . $buffer->toString('hex') . "\n";

    $parser = new EtdSolutions\IPP\Parser();
    $result = $parser->parse($buffer);
    print_r($result);
