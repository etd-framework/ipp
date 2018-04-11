<?php
/**
 * Part of the ETD Framework IPP Package
 *
 * @copyright   Copyright (C) 208 ETD Solutions. Tous droits réservés.
 * @license     Apache License 2.0; see LICENSE
 * @author      ETD Solutions https://etd-solutions.com
 */

namespace EtdSolutions\IPP;

function createDeferred($func) {

    $deferred             = new \stdClass();
    $deferred->isDeferred = true;
    $deferred->function   = $func;

    return $deferred;
}

function isDeferred($type) {

    return is_object($type) && $type->isDeferred;
}

function keyword($arr, $type = "keyword") {

    return [
        "type"     => $type,
        "keywords" => $arr
    ];
}

//some values for the keyword- but can include other 'name's
function keyword_name($arr) {

    return keyword($arr, "keyword | name");
}

//a keyword, name, or empty value
function keyword_name_novalue($arr) {

    return keyword($arr, "keyword | name | no-value");
}

//a keyword that groups another keyword's values together
function setof_keyword($arr) {

    return keyword($arr, "1setOf keyword");
}

//a keyword that groups [another keyword's values] or [names] together
function setof_keyword_name($arr) {

    return keyword($arr, "1setOf keyword | name");
}

class Data {

    /**
     * @var array
     */
    protected static $data;

    /**
     * @var int
     */
    protected static $MAX;

    protected static function seedTags() {
        // Tags
        // ------------

        $tags = [
            null,                                     // 0x00 http://tools.ietf.org/html/rfc2910#section-3.5.1
            "operation-attributes-tag",           // 0x01 http://tools.ietf.org/html/rfc2910#section-3.5.1
            "job-attributes-tag",                 // 0x02 http://tools.ietf.org/html/rfc2910#section-3.5.1
            "end-of-attributes-tag",              // 0x03 http://tools.ietf.org/html/rfc2910#section-3.5.1
            "printer-attributes-tag",             // 0x04 http://tools.ietf.org/html/rfc2910#section-3.5.1
            "unsupported-attributes-tag",         // 0x05 http://tools.ietf.org/html/rfc2910#section-3.5.1
            "subscription-attributes-tag",        // 0x06 http://tools.ietf.org/html/rfc3995#section-14
            "event-notification-attributes-tag",  // 0x07 http://tools.ietf.org/html/rfc3995#section-14
            "resource-attributes-tag",            // 0x08 http://tools.ietf.org/html/draft-ietf-ipp-get-resource-00#section-11    did not get standardized
            "document-attributes-tag",            // 0x09 ftp://ftp.pwg.org/pub/pwg/candidates/cs-ippdocobject10-20031031-5100.5.pdf
            null, null, null, null, null, null,                                // 0x0A - 0x0F
            "unsupported",                        // 0x10 http://tools.ietf.org/html/rfc2910#section-3.5.2
            "default",                            // 0x11 http://tools.ietf.org/html/rfc2910#section-3.5.2
            "unknown",                            // 0x12 http://tools.ietf.org/html/rfc2910#section-3.5.2
            "no-value",                           // 0x13 http://tools.ietf.org/html/rfc2910#section-3.5.2
            null,                                     // 0x14
            "not-settable",                       // 0x15 http://tools.ietf.org/html/rfc3380#section-8.1
            "delete-attribute",                   // 0x16 http://tools.ietf.org/html/rfc3380#section-8.2
            "admin-define",                       // 0x17 http://tools.ietf.org/html/rfc3380#section-8.3
            null, null, null, null, null, null, null, null, null,                             // 0x18 - 0x20
            "integer",                            // 0x21 http://tools.ietf.org/html/rfc2910#section-3.5.2
            "boolean",                            // 0x22 http://tools.ietf.org/html/rfc2910#section-3.5.2
            "enum",                               // 0x23 http://tools.ietf.org/html/rfc2910#section-3.5.2
            null, null, null, null, null, null, null, null, null, null, null, null,                          // 0x24 - 0x2Fnull
            "octetString",                        // 0x30 http://tools.ietf.org/html/rfc2910#section-3.5.2
            "dateTime",                           // 0x31 http://tools.ietf.org/html/rfc2910#section-3.5.2
            "resolution",                         // 0x32 http://tools.ietf.org/html/rfc2910#section-3.5.2
            "rangeOfInteger",                     // 0x33 http://tools.ietf.org/html/rfc2910#section-3.5.2
            "begCollection",                      // 0x34 http://tools.ietf.org/html/rfc3382#section-7.1
            "textWithLanguage",                   // 0x35 http://tools.ietf.org/html/rfc2910#section-3.5.2
            "nameWithLanguage",                   // 0x36 http://tools.ietf.org/html/rfc2910#section-3.5.2
            "endCollection",                      // 0x37 http://tools.ietf.org/html/rfc3382#section-7.1
            null, null, null, null, null, null, null, null, null,                             // 0x38 - 0x40
            "textWithoutLanguage",                // 0x41 http://tools.ietf.org/html/rfc2910#section-3.5.2
            "nameWithoutLanguage",                // 0x42 http://tools.ietf.org/html/rfc2910#section-3.5.2
            null,                                     // 0x43
            "keyword",                            // 0x44 http://tools.ietf.org/html/rfc2910#section-3.5.2
            "uri",                                // 0x45 http://tools.ietf.org/html/rfc2910#section-3.5.2
            "uriScheme",                          // 0x46 http://tools.ietf.org/html/rfc2910#section-3.5.2
            "charset",                            // 0x47 http://tools.ietf.org/html/rfc2910#section-3.5.2
            "naturalLanguage",                    // 0x48 http://tools.ietf.org/html/rfc2910#section-3.5.2
            "mimeMediaType",                      // 0x49 http://tools.ietf.org/html/rfc2910#section-3.5.2
            "memberAttrName"                      // 0x4A http://tools.ietf.org/html/rfc3382#section-7.1
        ];

        $tags[0x7F] = "extension"; // http://tools.ietf.org/html/rfc2910#section-3.5.2
        $tags       = Util::xref($tags);

        if (!isset(self::$data)) {
            self::$data = [];
        }

        self::$data["tags"] = $tags;
    }

    protected static function seedEnums() {

        // Enums
        // ------------

        $enums = [
            "document-state"        => Util::xref([
                // ftp://ftp.pwg.org/pub/pwg/candidates/cs-ippdocobject10-20031031-5100.5.pdf
                null, null, null,                                                                          // 0x00-0x02
                "pending",                                                                                 // 0x03
                null,                                                                                      // 0x04
                "processing",                                                                              // 0x05
                null,                                                                                      // 0x06
                "canceled",                                                                                // 0x07
                "aborted",                                                                                 // 0x08
                "completed"                                                                                // 0x09
            ]),
            "finishings"            => Util::xref([
                null, null, null,                                                                          // 0x00-0x02
                "none",                                                                                    // 0x03 http://tools.ietf.org/html/rfc2911#section-4.2.6
                "staple",                                                                                  // 0x04 http://tools.ietf.org/html/rfc2911#section-4.2.6
                "punch",                                                                                   // 0x05 http://tools.ietf.org/html/rfc2911#section-4.2.6
                "cover",                                                                                   // 0x06 http://tools.ietf.org/html/rfc2911#section-4.2.6
                "bind",                                                                                    // 0x07 http://tools.ietf.org/html/rfc2911#section-4.2.6
                "saddle-stitch",                                                                           // 0x08 http://tools.ietf.org/html/rfc2911#section-4.2.6
                "edge-stitch",                                                                             // 0x09 http://tools.ietf.org/html/rfc2911#section-4.2.6
                "fold",                                                                                    // 0x0A http://tools.ietf.org/html/rfc2911#section-4.2.6
                "trim",                                                                                    // 0x0B ftp://ftp.pwg.org/pub/pwg/ipp/new_VAL/pwg5100.1.pdf
                "bale",                                                                                    // 0x0C ftp://ftp.pwg.org/pub/pwg/ipp/new_VAL/pwg5100.1.pdf
                "booklet-maker",                                                                           // 0x0D ftp://ftp.pwg.org/pub/pwg/ipp/new_VAL/pwg5100.1.pdf
                "jog-offset",                                                                              // 0x0E ftp://ftp.pwg.org/pub/pwg/ipp/new_VAL/pwg5100.1.pdf
                null, null, null, null, null,                                                              // 0x0F - 0x13 reserved for future generic finishing enum values.
                "staple-top-left",                                                                         // 0x14 http://tools.ietf.org/html/rfc2911#section-4.2.6
                "staple-bottom-left",                                                                      // 0x15 http://tools.ietf.org/html/rfc2911#section-4.2.6
                "staple-top-right",                                                                        // 0x16 http://tools.ietf.org/html/rfc2911#section-4.2.6
                "staple-bottom-right",                                                                     // 0x17 http://tools.ietf.org/html/rfc2911#section-4.2.6
                "edge-stitch-left",                                                                        // 0x18 http://tools.ietf.org/html/rfc2911#section-4.2.6
                "edge-stitch-top",                                                                         // 0x19 http://tools.ietf.org/html/rfc2911#section-4.2.6
                "edge-stitch-right",                                                                       // 0x1A http://tools.ietf.org/html/rfc2911#section-4.2.6
                "edge-stitch-bottom",                                                                      // 0x1B http://tools.ietf.org/html/rfc2911#section-4.2.6
                "staple-dual-left",                                                                        // 0x1C http://tools.ietf.org/html/rfc2911#section-4.2.6
                "staple-dual-top",                                                                         // 0x1D http://tools.ietf.org/html/rfc2911#section-4.2.6
                "staple-dual-right",                                                                       // 0x1E http://tools.ietf.org/html/rfc2911#section-4.2.6
                "staple-dual-bottom",                                                                      // 0x1F http://tools.ietf.org/html/rfc2911#section-4.2.6
                null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, // 0x20 - 0x31 reserved for future specific stapling and stitching enum values.
                "bind-left",                                                                               // 0x32 ftp://ftp.pwg.org/pub/pwg/ipp/new_VAL/pwg5100.1.pdf
                "bind-top",                                                                                // 0x33 ftp://ftp.pwg.org/pub/pwg/ipp/new_VAL/pwg5100.1.pdf
                "bind-right",                                                                              // 0x34 ftp://ftp.pwg.org/pub/pwg/ipp/new_VAL/pwg5100.1.pdf
                "bind-bottom",                                                                             // 0x35 ftp://ftp.pwg.org/pub/pwg/ipp/new_VAL/pwg5100.1.pdf
                null, null, null, null, null, null,                                                             // 0x36 - 0x3B
                "trim-after-pages",                                                                        // 0x3C ftp://ftp.pwg.org/pub/pwg/candidates/cs-ippjobprinterext3v10-20120727-5100.13.pdf (IPP Everywhere)
                "trim-after-documents",                                                                    // 0x3D ftp://ftp.pwg.org/pub/pwg/candidates/cs-ippjobprinterext3v10-20120727-5100.13.pdf (IPP Everywhere)
                "trim-after-copies",                                                                       // 0x3E ftp://ftp.pwg.org/pub/pwg/candidates/cs-ippjobprinterext3v10-20120727-5100.13.pdf (IPP Everywhere)
                "trim-after-job"                                                                           // 0x3F ftp://ftp.pwg.org/pub/pwg/candidates/cs-ippjobprinterext3v10-20120727-5100.13.pdf (IPP Everywhere)
            ]),
            "operations-supported"  => Util::xref([
                null,                                                                 // 0x00
                null,                                                                 // 0x01
                "Print-Job",                                                      // 0x02 http://tools.ietf.org/html/rfc2911#section-3.2.1
                "Print-URI",                                                      // 0x03 http://tools.ietf.org/html/rfc2911#section-3.2.2
                "Validate-Job",                                                   // 0x04 http://tools.ietf.org/html/rfc2911#section-3.2.3
                "Create-Job",                                                     // 0x05 http://tools.ietf.org/html/rfc2911#section-3.2.4
                "Send-Document",                                                  // 0x06 http://tools.ietf.org/html/rfc2911#section-3.3.1
                "Send-URI",                                                       // 0x07 http://tools.ietf.org/html/rfc2911#section-3.3.2
                "Cancel-Job",                                                     // 0x08 http://tools.ietf.org/html/rfc2911#section-3.3.3
                "Get-Job-Attributes",                                             // 0x09 http://tools.ietf.org/html/rfc2911#section-3.3.4
                "Get-Jobs",                                                       // 0x0A http://tools.ietf.org/html/rfc2911#section-3.2.6
                "Get-Printer-Attributes",                                         // 0x0B http://tools.ietf.org/html/rfc2911#section-3.2.5
                "Hold-Job",                                                       // 0x0C http://tools.ietf.org/html/rfc2911#section-3.3.5
                "Release-Job",                                                    // 0x0D http://tools.ietf.org/html/rfc2911#section-3.3.6
                "Restart-Job",                                                    // 0x0E http://tools.ietf.org/html/rfc2911#section-3.3.7
                null,                                                                 // 0x0F
                "Pause-Printer",                                                  // 0x10 http://tools.ietf.org/html/rfc2911#section-3.2.7
                "Resume-Printer",                                                 // 0x11 http://tools.ietf.org/html/rfc2911#section-3.2.8
                "Purge-Jobs",                                                     // 0x12 http://tools.ietf.org/html/rfc2911#section-3.2.9
                "Set-Printer-Attributes",                                         // 0x13 IPP2.1 http://tools.ietf.org/html/rfc3380#section-4.1
                "Set-Job-Attributes",                                             // 0x14 IPP2.1 http://tools.ietf.org/html/rfc3380#section-4.2
                "Get-Printer-Supported-Values",                                   // 0x15 IPP2.1 http://tools.ietf.org/html/rfc3380#section-4.3
                "Create-Printer-Subscriptions",                                   // 0x16 IPP2.1 http://tools.ietf.org/html/rfc3995#section-7.1 && http://tools.ietf.org/html/rfc3995#section-11.1.2
                "Create-Job-Subscription",                                        // 0x17 IPP2.1 http://tools.ietf.org/html/rfc3995#section-7.1 && http://tools.ietf.org/html/rfc3995#section-11.1.1
                "Get-Subscription-Attributes",                                    // 0x18 IPP2.1 http://tools.ietf.org/html/rfc3995#section-7.1 && http://tools.ietf.org/html/rfc3995#section-11.2.4
                "Get-Subscriptions",                                              // 0x19 IPP2.1 http://tools.ietf.org/html/rfc3995#section-7.1 && http://tools.ietf.org/html/rfc3995#section-11.2.5
                "Renew-Subscription",                                             // 0x1A IPP2.1 http://tools.ietf.org/html/rfc3995#section-7.1 && http://tools.ietf.org/html/rfc3995#section-11.2.6
                "Cancel-Subscription",                                            // 0x1B IPP2.1 http://tools.ietf.org/html/rfc3995#section-7.1 && http://tools.ietf.org/html/rfc3995#section-11.2.7
                "Get-Notifications",                                              // 0x1C IPP2.1 IPP2.1 http://tools.ietf.org/html/rfc3996#section-9.2 && http://tools.ietf.org/html/rfc3996#section-5
                "ipp-indp-method",                                                // 0x1D did not get standardized
                "Get-Resource-Attributes",                                        // 0x1E http://tools.ietf.org/html/draft-ietf-ipp-get-resource-00#section-4.1 did not get standardized
                "Get-Resource-Data",                                              // 0x1F http://tools.ietf.org/html/draft-ietf-ipp-get-resource-00#section-4.2 did not get standardized
                "Get-Resources",                                                  // 0x20 http://tools.ietf.org/html/draft-ietf-ipp-get-resource-00#section-4.3 did not get standardized
                "ipp-install",                                                    // 0x21 did not get standardized
                "Enable-Printer",                                                 // 0x22 http://tools.ietf.org/html/rfc3998#section-3.1.1
                "Disable-Printer",                                                // 0x23 http://tools.ietf.org/html/rfc3998#section-3.1.2
                "Pause-Printer-After-Current-Job",                                // 0x24 http://tools.ietf.org/html/rfc3998#section-3.2.1
                "Hold-New-Jobs",                                                  // 0x25 http://tools.ietf.org/html/rfc3998#section-3.3.1
                "Release-Held-New-Jobs",                                          // 0x26 http://tools.ietf.org/html/rfc3998#section-3.3.2
                "Deactivate-Printer",                                             // 0x27 http://tools.ietf.org/html/rfc3998#section-3.4.1
                "Activate-Printer",                                               // 0x28 http://tools.ietf.org/html/rfc3998#section-3.4.2
                "Restart-Printer",                                                // 0x29 http://tools.ietf.org/html/rfc3998#section-3.5.1
                "Shutdown-Printer",                                               // 0x2A http://tools.ietf.org/html/rfc3998#section-3.5.2
                "Startup-Printer",                                                // 0x2B http://tools.ietf.org/html/rfc3998#section-3.5.3
                "Reprocess-Job",                                                  // 0x2C http://tools.ietf.org/html/rfc3998#section-4.1
                "Cancel-Current-Job",                                             // 0x2D http://tools.ietf.org/html/rfc3998#section-4.2
                "Suspend-Current-Job",                                            // 0x2E http://tools.ietf.org/html/rfc3998#section-4.3.1
                "Resume-Job",                                                     // 0x2F http://tools.ietf.org/html/rfc3998#section-4.3.2
                "Promote-Job",                                                    // 0x30 http://tools.ietf.org/html/rfc3998#section-4.4.1
                "Schedule-Job-After",                                             // 0x31 http://tools.ietf.org/html/rfc3998#section-4.4.2
                null,                                                                 // 0x32
                "Cancel-Document",                                                // 0x33 ftp://ftp.pwg.org/pub/pwg/candidates/cs-ippdocobject10-20031031-5100.5.pdf
                "Get-Document-Attributes",                                        // 0x34 ftp://ftp.pwg.org/pub/pwg/candidates/cs-ippdocobject10-20031031-5100.5.pdf
                "Get-Documents",                                                  // 0x35 ftp://ftp.pwg.org/pub/pwg/candidates/cs-ippdocobject10-20031031-5100.5.pdf
                "Delete-Document",                                                // 0x36 ftp://ftp.pwg.org/pub/pwg/candidates/cs-ippdocobject10-20031031-5100.5.pdf
                "Set-Document-Attributes",                                        // 0x37 ftp://ftp.pwg.org/pub/pwg/candidates/cs-ippdocobject10-20031031-5100.5.pdf
                "Cancel-Jobs",                                                    // 0x38 ftp://ftp.pwg.org/pub/pwg/candidates/cs-ippjobprinterext10-20101030-5100.11.pdf
                "Cancel-My-Jobs",                                                 // 0x39 ftp://ftp.pwg.org/pub/pwg/candidates/cs-ippjobprinterext10-20101030-5100.11.pdf
                "Resubmit-Job",                                                   // 0x3A ftp://ftp.pwg.org/pub/pwg/candidates/cs-ippjobprinterext10-20101030-5100.11.pdf
                "Close-Job",                                                      // 0x3B ftp://ftp.pwg.org/pub/pwg/candidates/cs-ippjobprinterext10-20101030-5100.11.pdf
                "Identify-Printer",                                               // 0x3C ftp://ftp.pwg.org/pub/pwg/candidates/cs-ippjobprinterext3v10-20120727-5100.13.pdf
                "Validate-Document"                                               // 0x3D ftp://ftp.pwg.org/pub/pwg/candidates/cs-ippjobprinterext3v10-20120727-5100.13.pdf
            ]),
            "job-collation-type"    => Util::xref([
                // IPP2.1 http://tools.ietf.org/html/rfc3381#section-6.3
                "other",                                                          // 0x01
                "unknown",                                                        // 0x02
                "uncollated-documents",                                           // 0x03
                'collated-documents',                                             // 0x04
                'uncollated-documents'                                            // 0x05
            ]),
            "job-state"             => Util::xref([
                // http://tools.ietf.org/html/rfc2911#section-4.3.7
                null, null, null,                                                               // 0x00-0x02
                "pending",                                                        // 0x03
                "pending-held",                                                   // 0x04
                "processing",                                                     // 0x05
                "processing-stopped",                                             // 0x06
                "canceled",                                                       // 0x07
                "aborted",                                                        // 0x08
                "completed"                                                       // 0x09
            ]),
            "orientation-requested" => Util::xref([
                // http://tools.ietf.org/html/rfc2911#section-4.2.10
                null, null, null,                                                               // 0x00-0x02
                "portrait",                                                       // 0x03
                "landscape",                                                      // 0x04
                "reverse-landscape",                                              // 0x05
                "reverse-portrait",                                               // 0x06
                "none"                                                            // 0x07 ftp://ftp.pwg.org/pub/pwg/candidates/cs-ippjobprinterext3v10-20120727-5100.13.pdf
            ]),
            "print-quality"         => Util::xref([
                // http://tools.ietf.org/html/rfc2911#section-4.2.13
                null, null, null,                                                               // 0x00-0x02
                "draft",                                                          // 0x03
                "normal",                                                         // 0x04
                "high"                                                            // 0x05
            ]),
            "printer-state"         => Util::xref([
                // http://tools.ietf.org/html/rfc2911#section-4.4.11
                null, null, null,                                                               // 0x00-0x02
                "idle",                                                           // 0x03
                "processing",                                                     // 0x04
                "stopped"                                                         // 0x05
            ])
        ];

        $enums["finishings-default"]              = $enums["finishings"];
        $enums["finishings-ready"]                = $enums["finishings"];
        $enums["finishings-supported"]            = $enums["finishings"];
        $enums["media-source-feed-orientation"]   = $enums["orientation-requested"];
        $enums["orientation-requested-default"]   = $enums["orientation-requested"];
        $enums["orientation-requested-supported"] = $enums["orientation-requested"];//1setOf
        $enums["print-quality-default"]           = $enums["print-quality"];
        $enums["print-quality-supported"]         = $enums["print-quality"];//1setOf

        if (!isset(self::$data)) {
            self::$data = [];
        }

        self::$data["enums"] = $enums;

    }

    protected static function seedVersions() {

        // Versions
        // ------------

        $versions = Util::xref([
            0x0100 => '1.0',
            0x0101 => '1.1',
            0x0200 => '2.0',
            0x0201 => '2.1'
        ]);

        if (!isset(self::$data)) {
            self::$data = [];
        }

        self::$data["versions"] = $versions;
    }

    protected static function seedAttributes() {

        // Attributes
        // ------------

        /*
            The attributes and their syntaxes are complicated. The functions in this
            file serve as syntactic sugar that allow the attribute definitions to remain
            close to what you will see in the spec. A bit of processing is done at the end
            to convert it to one big object tree.
        */

        $attributes = [
            "Document Description"     => [
                "attributes-charset"                      => "EtdSolutions\IPP\Data::charset",
                "attributes-natural-language"             => "EtdSolutions\IPP\Data::naturalLanguage",
                "compression"                             => "EtdSolutions\IPP\Data::keyword",
                "copies-actual"                           => self::setof(self::integer(1, self::$MAX)),
                "cover-back-actual"                       => self::setof(self::collection("Job Template", "cover-back")),
                "cover-front-actual"                      => self::setof(self::collection("Job Template", "cover-front")),
                "current-page-order"                      => "EtdSolutions\IPP\Data::keyword",
                "date-time-at-completed"                  => self::underscore("EtdSolutions\IPP\Data::dateTime", "EtdSolutions\IPP\Data::novalue"),
                "date-time-at-creation"                   => "EtdSolutions\IPP\Data::dateTime",
                "date-time-at-processing"                 => self::underscore("EtdSolutions\IPP\Data::dateTime", "EtdSolutions\IPP\Data::novalue"),
                "detailed-status-messages"                => self::setof("EtdSolutions\IPP\Data::text"),
                "document-access-errors"                  => self::setof("EtdSolutions\IPP\Data::text"),
                "document-charset"                        => "EtdSolutions\IPP\Data::charset",
                "document-digital-signature"              => "EtdSolutions\IPP\Data::keyword",
                "document-format"                         => "EtdSolutions\IPP\Data::mimeMediaType",
                "document-format-details"                 => self::setof(self::collection("Operation", "document-format-details")),
                "document-format-details-detected"        => self::setof(self::collection("Operation", "document-format-details")),
                "document-format-detected"                => "EtdSolutions\IPP\Data::mimeMediaType",
                "document-format-version"                 => self::text(127),
                "document-format-version-detected"        => self::text(127),
                "document-job-id"                         => self::integer(1, self::$MAX),
                "document-job-uri"                        => "EtdSolutions\IPP\Data::uri",
                "document-message"                        => "EtdSolutions\IPP\Data::text",
                "document-metadata"                       => self::setof("EtdSolutions\IPP\Data::octetString"),
                "document-name"                           => "EtdSolutions\IPP\Data::name",
                "document-natural-language"               => "EtdSolutions\IPP\Data::naturalLanguage",
                "document-number"                         => self::integer(1, self::$MAX),
                "document-printer-uri"                    => "EtdSolutions\IPP\Data::uri",
                "document-state"                          => "EtdSolutions\IPP\Data::enumeration",
                "document-state-message"                  => "EtdSolutions\IPP\Data::text",
                "document-state-reasons"                  => self::setof("EtdSolutions\IPP\Data::keyword"),
                "document-uri"                            => "EtdSolutions\IPP\Data::uri",
                "document-uuid"                           => self::uri(45),
                "errors-count"                            => self::integer(0, self::$MAX),
                "finishings-actual"                       => self::setof("EtdSolutions\IPP\Data::enumeration"),
                "finishings-col-actual"                   => self::setof(self::collection("Job Template", "finishings-col")),
                "force-front-side-actual"                 => self::setof(self::integer(1, self::$MAX)),
                "imposition-template-actual"              => self::setof(self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name")),
                "impressions"                             => self::integer(0, self::$MAX),
                "impressions-completed"                   => self::integer(0, self::$MAX),
                "impressions-completed-current-copy"      => self::integer(0, self::$MAX),
                "insert-sheet-actual"                     => self::setof(self::collection("Job Template", "insert-sheet")),
                "k-octets"                                => self::integer(0, self::$MAX),
                "k-octets-processed"                      => self::integer(0, self::$MAX),
                "last-document"                           => "EtdSolutions\IPP\Data::boolean",
                "media-actual"                            => self::setof(self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name")),
                "media-col-actual"                        => self::setof(self::collection("Job Template", "media-col")),
                "media-input-tray-check-actual"           => self::setof(self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name")),
                "media-sheets"                            => self::integer(0, self::$MAX),
                "media-sheets-completed"                  => self::integer(0, self::$MAX),
                "more-info"                               => "EtdSolutions\IPP\Data::uri",
                "number-up-actual"                        => self::setof("EtdSolutions\IPP\Data::integer"),
                "orientation-requested-actual"            => self::setof("EtdSolutions\IPP\Data::enumeration"),
                "output-bin-actual"                       => self::setof("EtdSolutions\IPP\Data::name"),
                "output-device-assigned"                  => self::name(127),
                "overrides-actual"                        => self::setof(self::collection("Document Template", "overrides")),
                "page-delivery-actual"                    => self::setof("EtdSolutions\IPP\Data::keyword"),
                "page-order-received-actual"              => self::setof("EtdSolutions\IPP\Data::keyword"),
                "page-ranges-actual"                      => self::setof(self::rangeOfInteger(1, self::$MAX)),
                "pages"                                   => self::integer(0, self::$MAX),
                "pages-completed"                         => self::integer(0, self::$MAX),
                "pages-completed-current-copy"            => self::integer(0, self::$MAX),
                "presentation-direction-number-up-actual" => self::setof("EtdSolutions\IPP\Data::keyword"),
                "print-content-optimize-actual"           => self::setof("EtdSolutions\IPP\Data::keyword"),
                "print-quality-actual"                    => self::setof("EtdSolutions\IPP\Data::enumeration"),
                "printer-resolution-actual"               => self::setof("EtdSolutions\IPP\Data::resolution"),
                "printer-up-time"                         => self::integer(1, self::$MAX),
                "separator-sheets-actual"                 => self::setof(self::collection("Job Template", "separator-sheets")),
                "sheet-completed-copy-number"             => self::integer(0, self::$MAX),
                "sides-actual"                            => self::setof("EtdSolutions\IPP\Data::keyword"),
                "time-at-completed"                       => self::underscore("EtdSolutions\IPP\Data::integer", "EtdSolutions\IPP\Data::novalue"),
                "time-at-creation"                        => "EtdSolutions\IPP\Data::integer",
                "time-at-processing"                      => self::underscore("EtdSolutions\IPP\Data::integer", "EtdSolutions\IPP\Data::novalue"),
                "x-image-position-actual"                 => self::setof("EtdSolutions\IPP\Data::keyword"),
                "x-image-shift-actual"                    => self::setof("EtdSolutions\IPP\Data::integer"),
                "x-side1-image-shift-actual"              => self::setof("EtdSolutions\IPP\Data::integer"),
                "x-side2-image-shift-actual"              => self::setof("EtdSolutions\IPP\Data::integer"),
                "y-image-position-actual"                 => self::setof("EtdSolutions\IPP\Data::keyword"),
                "y-image-shift-actual"                    => self::setof("EtdSolutions\IPP\Data::integer"),
                "y-side1-image-shift-actual"              => self::setof("EtdSolutions\IPP\Data::integer"),
                "y-side2-image-shift-actual"              => self::setof("EtdSolutions\IPP\Data::integer")
            ],
            "Document Template"        => [
                "copies"                           => self::integer(1, self::$MAX),
                "cover-back"                       => self::collection("Job Template", "cover-back"),
                "cover-front"                      => self::collection("Job Template", "cover-front"),
                "feed-orientation"                 => "EtdSolutions\IPP\Data::keyword",
                "finishings"                       => self::setof("EtdSolutions\IPP\Data::enumeration"),
                "finishings-col"                   => self::collection("Job Template", "finishings-col"),
                "font-name-requested"              => "EtdSolutions\IPP\Data::name",
                "font-size-requested"              => self::integer(1, self::$MAX),
                "force-front-side"                 => self::setof(self::integer(1, self::$MAX)),
                "imposition-template"              => self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name"),
                "insert-sheet"                     => self::setof(self::collection("Job Template", "insert-sheet")),
                "media"                            => self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name"),
                "media-col"                        => self::collection("Job Template", "media-col"),
                "media-input-tray-check"           => self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name"),
                "number-up"                        => self::integer(1, self::$MAX),
                "orientation-requested"            => "EtdSolutions\IPP\Data::enumeration",
                "overrides"                        => self::setof(self::collection([
                    //Any Document Template attribute (TODO)
                    "document-copies"  => self::setof("EtdSolutions\IPP\Data::rangeOfInteger"),
                    "document-numbers" => self::setof("EtdSolutions\IPP\Data::rangeOfInteger"),
                    "pages"            => self::setof("EtdSolutions\IPP\Data::rangeOfInteger")
                ])),
                "page-delivery"                    => "EtdSolutions\IPP\Data::keyword",
                "page-order-received"              => "EtdSolutions\IPP\Data::keyword",
                "page-ranges"                      => self::setof(self::rangeOfInteger(1, self::$MAX)),
                "pdl-init-file"                    => self::setof(self::collection("Job Template", "pdl-init-file")),
                "presentation-direction-number-up" => "EtdSolutions\IPP\Data::keyword",
                "print-color-mode"                 => "EtdSolutions\IPP\Data::keyword",
                "print-content-optimize"           => "EtdSolutions\IPP\Data::keyword",
                "print-quality"                    => "EtdSolutions\IPP\Data::enumeration",
                "print-rendering-intent"           => "EtdSolutions\IPP\Data::keyword",
                "printer-resolution"               => "EtdSolutions\IPP\Data::resolution",
                "separator-sheets"                 => self::collection("Job Template", "separator-sheets"),
                "sheet-collate"                    => "EtdSolutions\IPP\Data::keyword",
                "sides"                            => "EtdSolutions\IPP\Data::keyword",
                "x-image-position"                 => "EtdSolutions\IPP\Data::keyword",
                "x-image-shift"                    => "EtdSolutions\IPP\Data::integer",
                "x-side1-image-shift"              => "EtdSolutions\IPP\Data::integer",
                "x-side2-image-shift"              => "EtdSolutions\IPP\Data::integer",
                "y-image-position"                 => "EtdSolutions\IPP\Data::keyword",
                "y-image-shift"                    => "EtdSolutions\IPP\Data::integer",
                "y-side1-image-shift"              => "EtdSolutions\IPP\Data::integer",
                "y-side2-image-shift"              => "EtdSolutions\IPP\Data::integer"
            ],
            "Event Notifications"      => [
                "notify-subscribed-event" => "EtdSolutions\IPP\Data::keyword",
                "notify-text"             => "EtdSolutions\IPP\Data::text",
            ],
            "Job Description"          => [
                "attributes-charset"                      => "EtdSolutions\IPP\Data::charset",
                "attributes-natural-language"             => "EtdSolutions\IPP\Data::naturalLanguage",
                "compression-supplied"                    => "EtdSolutions\IPP\Data::keyword",
                "copies-actual"                           => self::setof(self::integer(1, self::$MAX)),
                "cover-back-actual"                       => self::setof(self::collection("Job Template", "cover-back")),
                "cover-front-actual"                      => self::setof(self::collection("Job Template", "cover-front")),
                "current-page-order"                      => "EtdSolutions\IPP\Data::keyword",
                "date-time-at-completed"                  => self::underscore("EtdSolutions\IPP\Data::dateTime", "EtdSolutions\IPP\Data::novalue"),
                "date-time-at-creation"                   => "EtdSolutions\IPP\Data::dateTime",
                "date-time-at-processing"                 => self::underscore("EtdSolutions\IPP\Data::dateTime", "EtdSolutions\IPP\Data::novalue"),
                "document-charset-supplied"               => "EtdSolutions\IPP\Data::charset",
                "document-digital-signature-supplied"     => "EtdSolutions\IPP\Data::keyword",
                "document-format-details-supplied"        => self::setof(self::collection("Operation", "document-format-details")),
                "document-format-supplied"                => "EtdSolutions\IPP\Data::mimeMediaType",
                "document-format-version-supplied"        => self::text(127),
                "document-message-supplied"               => "EtdSolutions\IPP\Data::text",
                "document-metadata"                       => self::setof("EtdSolutions\IPP\Data::octetString"),
                "document-name-supplied"                  => "EtdSolutions\IPP\Data::name",
                "document-natural-language-supplied"      => "EtdSolutions\IPP\Data::naturalLanguage",
                "document-overrides-actual"               => self::setof("EtdSolutions\IPP\Data::collection"),
                "errors-count"                            => self::integer(0, self::$MAX),
                "finishings-actual"                       => self::setof("EtdSolutions\IPP\Data::enumeration"),
                "finishings-col-actual"                   => self::setof(self::collection("Job Template", "finishings-col")),
                "force-front-side-actual"                 => self::setof(self::setof(self::integer(1, self::$MAX))),
                "imposition-template-actual"              => self::setof(self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name")),
                "impressions-completed-current-copy"      => self::integer(0, self::$MAX),
                "insert-sheet-actual"                     => self::setof(self::collection("Job Template", "insert-sheet")),
                "job-account-id-actual"                   => self::setof("EtdSolutions\IPP\Data::name"),
                "job-accounting-sheets-actual"            => self::setof(self::collection("Job Template", "job-accounting-sheets")),
                "job-accounting-user-id-actual"           => self::setof("EtdSolutions\IPP\Data::name"),
                "job-attribute-fidelity"                  => "EtdSolutions\IPP\Data::boolean",
                "job-collation-type"                      => "EtdSolutions\IPP\Data::enumeration",
                "job-collation-type-actual"               => self::setof("EtdSolutions\IPP\Data::keyword"),
                "job-copies-actual"                       => self::setof(self::integer(1, self::$MAX)),
                "job-cover-back-actual"                   => self::setof(self::collection("Job Template", "cover-back")),
                "job-cover-front-actual"                  => self::setof(self::collection("Job Template", "cover-front")),
                "job-detailed-status-messages"            => self::setof("EtdSolutions\IPP\Data::text"),
                "job-document-access-errors"              => self::setof("EtdSolutions\IPP\Data::text"),
                "job-error-sheet-actual"                  => self::setof(self::collection("Job Template", "job-error-sheet")),
                "job-finishings-actual"                   => self::setof("EtdSolutions\IPP\Data::enumeration"),
                "job-finishings-col-actual"               => self::setof(self::collection("Job Template", "media-col")),
                "job-hold-until-actual"                   => self::setof(self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name")),
                "job-id"                                  => self::integer(1, self::$MAX),
                "job-impressions"                         => self::integer(0, self::$MAX),
                "job-impressions-completed"               => self::integer(0, self::$MAX),
                "job-k-octets"                            => self::integer(0, self::$MAX),
                "job-k-octets-processed"                  => self::integer(0, self::$MAX),
                "job-mandatory-attributes"                => self::setof("EtdSolutions\IPP\Data::keyword"),
                "job-media-sheets"                        => self::integer(0, self::$MAX),
                "job-media-sheets-completed"              => self::integer(0, self::$MAX),
                "job-message-from-operator"               => self::text(127),
                "job-message-to-operator-actual"          => self::setof("EtdSolutions\IPP\Data::text"),
                "job-more-info"                           => "EtdSolutions\IPP\Data::uri",
                "job-name"                                => "EtdSolutions\IPP\Data::name",
                "job-originating-user-name"               => "EtdSolutions\IPP\Data::name",
                "job-originating-user-uri"                => "EtdSolutions\IPP\Data::uri",
                "job-pages"                               => self::integer(0, self::$MAX),
                "job-pages-completed"                     => self::integer(0, self::$MAX),
                "job-pages-completed-current-copy"        => self::integer(0, self::$MAX),
                "job-printer-up-time"                     => self::integer(1, self::$MAX),
                "job-printer-uri"                         => "EtdSolutions\IPP\Data::uri",
                "job-priority-actual"                     => self::setof(self::integer(1, 100)),
                "job-save-printer-make-and-model"         => self::text(127),
                "job-sheet-message-actual"                => self::setof("EtdSolutions\IPP\Data::text"),
                "job-sheets-actual"                       => self::setof(self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name")),
                "job-sheets-col-actual"                   => self::setof(self::collection("Job Template", "job-sheets-col")),
                "job-state"                               => self::underscore("EtdSolutions\IPP\Data::enumeration", "EtdSolutions\IPP\Data::unknown"),
                "job-state-message"                       => "EtdSolutions\IPP\Data::text",
                "job-state-reasons"                       => self::setof("EtdSolutions\IPP\Data::keyword"),
                "job-uri"                                 => "EtdSolutions\IPP\Data::uri",
                "job-uuid"                                => self::uri(45),
                "media-actual"                            => self::setof(self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name")),
                "media-col-actual"                        => self::setof(self::collection("Job Template", "media-col")),
                "media-input-tray-check-actual"           => self::setof(self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name")),
                "multiple-document-handling-actual"       => self::setof("EtdSolutions\IPP\Data::keyword"),
                "number-of-documents"                     => self::integer(0, self::$MAX),
                "number-of-intervening-jobs"              => self::integer(0, self::$MAX),
                "number-up-actual"                        => self::setof(self::integer(1, self::$MAX)),
                "orientation-requested-actual"            => self::setof("EtdSolutions\IPP\Data::enumeration"),
                "original-requesting-user-name"           => "EtdSolutions\IPP\Data::name",
                "output-bin-actual"                       => self::setof(self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name")),
                "output-device-actual"                    => self::setof(self::name(127)),
                "output-device-assigned"                  => self::name(127),
                "overrides-actual"                        => self::setof(self::collection("Job Template", "overrides")),
                "page-delivery-actual"                    => self::setof("EtdSolutions\IPP\Data::keyword"),
                "page-order-received-actual"              => self::setof("EtdSolutions\IPP\Data::keyword"),
                "page-ranges-actual"                      => self::setof(self::rangeOfInteger(1, self::$MAX)),
                "presentation-direction-number-up-actual" => self::setof("EtdSolutions\IPP\Data::keyword"),
                "print-content-optimize-actual"           => self::setof("EtdSolutions\IPP\Data::keyword"),
                "print-quality-actual"                    => self::setof("EtdSolutions\IPP\Data::enumeration"),
                "printer-resolution-actual"               => self::setof("EtdSolutions\IPP\Data::resolution"),
                "separator-sheets-actual"                 => self::setof(self::collection("Job Template", "separator-sheets")),
                "sheet-collate-actual"                    => self::setof("EtdSolutions\IPP\Data::keyword"),
                "sheet-completed-copy-number"             => self::integer(0, self::$MAX),
                "sheet-completed-document-number"         => self::integer(0, self::$MAX),
                "sides-actual"                            => self::setof("EtdSolutions\IPP\Data::keyword"),
                "time-at-completed"                       => self::underscore("EtdSolutions\IPP\Data::integer", "EtdSolutions\IPP\Data::novalue"),
                "time-at-creation"                        => "EtdSolutions\IPP\Data::integer",
                "time-at-processing"                      => self::underscore("EtdSolutions\IPP\Data::integer", "EtdSolutions\IPP\Data::novalue"),
                "warnings-count"                          => self::integer(0, self::$MAX),
                "x-image-position-actual"                 => self::setof("EtdSolutions\IPP\Data::keyword"),
                "x-image-shift-actual"                    => self::setof("EtdSolutions\IPP\Data::integer"),
                "x-side1-image-shift-actual"              => self::setof("EtdSolutions\IPP\Data::integer"),
                "x-side2-image-shift-actual"              => self::setof("EtdSolutions\IPP\Data::integer"),
                "y-image-position-actual"                 => self::setof("EtdSolutions\IPP\Data::keyword"),
                "y-image-shift-actual"                    => self::setof("EtdSolutions\IPP\Data::integer"),
                "y-side1-image-shift-actual"              => self::setof("EtdSolutions\IPP\Data::integer"),
                "y-side2-image-shift-actual"              => self::setof("EtdSolutions\IPP\Data::integer")
            ],
            "Job Template"             => [
                "copies"                           => self::integer(1, self::$MAX),
                "cover-back"                       => self::collection([
                    "cover-type" => "EtdSolutions\IPP\Data::keyword",
                    "media"      => self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name"),
                    "media-col"  => self::collection("Job Template", "media-col")
                ]),
                "cover-front"                      => self::collection([
                    "cover-type" => "EtdSolutions\IPP\Data::keyword",
                    "media"      => self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name"),
                    "media-col"  => self::collection("Job Template", "media-col")
                ]),
                "feed-orientation"                 => "EtdSolutions\IPP\Data::keyword",
                "finishings"                       => self::setof("EtdSolutions\IPP\Data::enumeration"),
                "finishings-col"                   => self::collection([
                    "finishing-template" => "EtdSolutions\IPP\Data::name",
                    "stitching"          => self::collection([
                        "stitching-locations"      => self::setof(self::integer(0, self::$MAX)),
                        "stitching-offset"         => self::integer(0, self::$MAX),
                        "stitching-reference-edge" => "EtdSolutions\IPP\Data::keyword"
                    ])
                ]),
                "font-name-requested"              => "EtdSolutions\IPP\Data::name",
                "font-size-requested"              => self::integer(1, self::$MAX),
                "force-front-side"                 => self::setof(self::integer(1, self::$MAX)),
                "imposition-template"              => self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name"),
                "insert-sheet"                     => self::setof(self::collection([
                    "insert-after-page-number" => self::integer(0, self::$MAX),
                    "insert-count"             => self::integer(0, self::$MAX),
                    "media"                    => self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name"),
                    "media-col"                => self::collection("Job Template", "media-col")
                ])),
                "job-account-id"                   => "EtdSolutions\IPP\Data::name",
                "job-accounting-sheets"            => self::collection([
                    "job-accounting-output-bin"  => self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name"),
                    "job-accounting-sheets-type" => self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name"),
                    "media"                      => self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name"),
                    "media-col"                  => self::collection("Job Template", "media-col")
                ]),
                "job-accounting-user-id"           => "EtdSolutions\IPP\Data::name",
                "job-copies"                       => self::integer(1, self::$MAX),
                "job-cover-back"                   => self::collection("Job Template", "cover-back"),
                "job-cover-front"                  => self::collection("Job Template", "cover-front"),
                "job-delay-output-until"           => self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name"),
                "job-delay-output-until-time"      => "EtdSolutions\IPP\Data::dateTime",
                "job-error-action"                 => "EtdSolutions\IPP\Data::keyword",
                "job-error-sheet"                  => self::collection([
                    "job-error-sheet-type" => self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name"),
                    "job-error-sheet-when" => "EtdSolutions\IPP\Data::keyword",
                    "media"                => self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name"),
                    "media-col"            => self::collection("Job Template", "media-col")
                ]),
                "job-finishings"                   => self::setof("EtdSolutions\IPP\Data::enumeration"),
                "job-finishings-col"               => self::collection("Job Template", "finishings-col"),
                "job-hold-until"                   => self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name"),
                "job-hold-until-time"              => "EtdSolutions\IPP\Data::dateTime",
                "job-message-to-operator"          => "EtdSolutions\IPP\Data::text",
                "job-phone-number"                 => "EtdSolutions\IPP\Data::uri",
                "job-priority"                     => self::integer(1, 100),
                "job-recipient-name"               => "EtdSolutions\IPP\Data::name",
                "job-save-disposition"             => self::collection([
                    "save-disposition" => "EtdSolutions\IPP\Data::keyword",
                    "save-info"        => self::setof(self::collection([
                        "save-document-format" => "EtdSolutions\IPP\Data::mimeMediaType",
                        "save-location"        => "EtdSolutions\IPP\Data::uri",
                        "save-name"            => "EtdSolutions\IPP\Data::name"
                    ]))
                ]),
                "job-sheet-message"                => "EtdSolutions\IPP\Data::text",
                "job-sheets"                       => self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name"),
                "job-sheets-col"                   => self::collection([
                    "job-sheets" => self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name"),
                    "media"      => self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name"),
                    "media-col"  => self::collection("Job Template", "media-col")
                ]),
                "media"                            => self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name"),
                "media-col"                        => self::collection([
                    "media-back-coating"  => self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name"),
                    "media-bottom-margin" => self::integer(0, self::$MAX),
                    "media-color"         => self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name"),
                    "media-front-coating" => self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name"),
                    "media-grain"         => self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name"),
                    "media-hole-count"    => self::integer(0, self::$MAX),
                    "media-info"          => self::text(255),
                    "media-key"           => self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name"),
                    "media-left-margin"   => self::integer(0, self::$MAX),
                    "media-order-count"   => self::integer(1, self::$MAX),
                    "media-pre-printed"   => self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name"),
                    "media-recycled"      => self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name"),
                    "media-right-margin"  => self::integer(0, self::$MAX),
                    "media-size"          => self::collection([
                        "x-dimension" => self::integer(0, self::$MAX),
                        "y-dimension" => self::integer(0, self::$MAX),
                    ]),
                    "media-size-name"     => self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name"),
                    "media-source"        => self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name"),
                    "media-thickness"     => self::integer(1, self::$MAX),
                    "media-tooth"         => self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name"),
                    "media-top-margin"    => self::integer(0, self::$MAX),
                    "media-type"          => self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name"),
                    "media-weight-metric" => self::integer(0, self::$MAX)
                ]),
                "media-input-tray-check"           => self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name"),
                "multiple-document-handling"       => "EtdSolutions\IPP\Data::keyword",
                "number-up"                        => self::integer(1, self::$MAX),
                "orientation-requested"            => "EtdSolutions\IPP\Data::enumeration",
                "output-bin"                       => self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name"),
                "output-device"                    => self::name(127),
                "overrides"                        => self::setof(self::collection([

                    //Any Job Template attribute (TODO)
                    "document-copies"  => self::setof("EtdSolutions\IPP\Data::rangeOfInteger"),
                    "document-numbers" => self::setof("EtdSolutions\IPP\Data::rangeOfInteger"),
                    "pages"            => self::setof("EtdSolutions\IPP\Data::rangeOfInteger")
                ])),
                "page-delivery"                    => "EtdSolutions\IPP\Data::keyword",
                "page-order-received"              => "EtdSolutions\IPP\Data::keyword",
                "page-ranges"                      => self::setof(self::rangeOfInteger(1, self::$MAX)),
                "pages-per-subset"                 => self::setof(self::integer(1, self::$MAX)),
                "pdl-init-file"                    => self::collection([
                    "pdl-init-file-entry"    => "EtdSolutions\IPP\Data::name",
                    "pdl-init-file-location" => "EtdSolutions\IPP\Data::uri",
                    "pdl-init-file-name"     => "EtdSolutions\IPP\Data::name"
                ]),
                "presentation-direction-number-up" => "EtdSolutions\IPP\Data::keyword",
                "print-color-mode"                 => "EtdSolutions\IPP\Data::keyword",
                "print-content-optimize"           => "EtdSolutions\IPP\Data::keyword",
                "print-quality"                    => "EtdSolutions\IPP\Data::enumeration",
                "print-rendering-intent"           => "EtdSolutions\IPP\Data::keyword",
                "printer-resolution"               => "EtdSolutions\IPP\Data::resolution",
                "proof-print"                      => self::collection([
                    "media"              => self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name"),
                    "media-col"          => self::collection("Job Template", "media-col"),
                    "proof-print-copies" => self::integer(0, self::$MAX)
                ]),
                "separator-sheets"                 => self::collection([
                    "media"                 => self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name"),
                    "media-col"             => self::collection("Job Template", "media-col"),
                    "separator-sheets-type" => self::setof("EtdSolutions\IPP\Data::keyword")
                ]),
                "sheet-collate"                    => "EtdSolutions\IPP\Data::keyword",
                "sides"                            => "EtdSolutions\IPP\Data::keyword",
                "x-image-position"                 => "EtdSolutions\IPP\Data::keyword",
                "x-image-shift"                    => "EtdSolutions\IPP\Data::integer",
                "x-side1-image-shift"              => "EtdSolutions\IPP\Data::integer",
                "x-side2-image-shift"              => "EtdSolutions\IPP\Data::integer",
                "y-image-position"                 => "EtdSolutions\IPP\Data::keyword",
                "y-image-shift"                    => "EtdSolutions\IPP\Data::integer",
                "y-side1-image-shift"              => "EtdSolutions\IPP\Data::integer",
                "y-side2-image-shift"              => "EtdSolutions\IPP\Data::integer"
            ],
            "Operation"                => [
                "attributes-charset"            => "EtdSolutions\IPP\Data::charset",
                "attributes-natural-language"   => "EtdSolutions\IPP\Data::naturalLanguage",
                "compression"                   => "EtdSolutions\IPP\Data::keyword",
                "detailed-status-message"       => "EtdSolutions\IPP\Data::text",
                "document-access-error"         => "EtdSolutions\IPP\Data::text",
                "document-charset"              => "EtdSolutions\IPP\Data::charset",
                "document-digital-signature"    => "EtdSolutions\IPP\Data::keyword",
                "document-format"               => "EtdSolutions\IPP\Data::mimeMediaType",
                "document-format-details"       => self::setof(self::collection([
                    "document-format"                     => "EtdSolutions\IPP\Data::mimeMediaType",
                    "document-format-device-id"           => self::text(127),
                    "document-format-version"             => self::text(127),
                    "document-natural-language"           => self::setof("EtdSolutions\IPP\Data::naturalLanguage"),
                    "document-source-application-name"    => "EtdSolutions\IPP\Data::name",
                    "document-source-application-version" => self::text(127),
                    "document-source-os-name"             => self::name(40),
                    "document-source-os-version"          => self::text(40)
                ])),
                "document-message"              => "EtdSolutions\IPP\Data::text",
                "document-metadata"             => self::setof("EtdSolutions\IPP\Data::octetString"),
                "document-name"                 => "EtdSolutions\IPP\Data::name",
                "document-natural-language"     => "EtdSolutions\IPP\Data::naturalLanguage",
                "document-password"             => "EtdSolutions\IPP\Data::octetString",
                "document-uri"                  => "EtdSolutions\IPP\Data::uri",
                "first-index"                   => self::integer(1, self::$MAX),
                "identify-actions"              => self::setof("EtdSolutions\IPP\Data::keyword"),
                "ipp-attribute-fidelity"        => "EtdSolutions\IPP\Data::boolean",
                "job-hold-until"                => self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name"),
                "job-id"                        => self::integer(1, self::$MAX),
                "job-ids"                       => self::setof(self::integer(1, self::$MAX)),
                "job-impressions"               => self::integer(0, self::$MAX),
                "job-k-octets"                  => self::integer(0, self::$MAX),
                "job-mandatory-attributes"      => self::setof("EtdSolutions\IPP\Data::keyword"),
                "job-media-sheets"              => self::integer(0, self::$MAX),
                "job-message-from-operator"     => self::text(127),
                "job-name"                      => "EtdSolutions\IPP\Data::name",
                "job-password"                  => self::octetString(255),
                "job-password-encryption"       => self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name"),
                "job-state"                     => "EtdSolutions\IPP\Data::enumeration",
                "job-state-message"             => "EtdSolutions\IPP\Data::text",
                "job-state-reasons"             => self::setof("EtdSolutions\IPP\Data::keyword"),
                "job-uri"                       => "EtdSolutions\IPP\Data::uri",
                "last-document"                 => "EtdSolutions\IPP\Data::boolean",
                "limit"                         => self::integer(1, self::$MAX),
                "message"                       => self::text(127),
                "my-jobs"                       => "EtdSolutions\IPP\Data::boolean",
                "original-requesting-user-name" => "EtdSolutions\IPP\Data::name",
                "preferred-attributes"          => "EtdSolutions\IPP\Data::collection",
                "printer-message-from-operator" => self::text(127),
                "printer-uri"                   => "EtdSolutions\IPP\Data::uri",
                "requested-attributes"          => self::setof("EtdSolutions\IPP\Data::keyword"),
                "requesting-user-name"          => "EtdSolutions\IPP\Data::name",
                "requesting-user-uri"           => "EtdSolutions\IPP\Data::uri",
                "status-message"                => self::text(255),
                "which-jobs"                    => "EtdSolutions\IPP\Data::keyword"
            ],
            "Printer Description"      => [
                "charset-configured"                         => "EtdSolutions\IPP\Data::charset",
                "charset-supported"                          => self::setof("EtdSolutions\IPP\Data::charset"),
                "color-supported"                            => "EtdSolutions\IPP\Data::boolean",
                "compression-supported"                      => self::setof("EtdSolutions\IPP\Data::keyword"),
                "copies-default"                             => self::integer(1, self::$MAX),
                "copies-supported"                           => self::rangeOfInteger(1, self::$MAX),
                "cover-back-default"                         => self::collection("Job Template", "cover-back"),
                "cover-back-supported"                       => self::setof("EtdSolutions\IPP\Data::keyword"),
                "cover-front-default"                        => self::collection("Job Template", "cover-front"),
                "cover-front-supported"                      => self::setof("EtdSolutions\IPP\Data::keyword"),
                "device-service-count"                       => self::integer(1, self::$MAX),
                "device-uuid"                                => self::uri(45),
                "document-charset-default"                   => "EtdSolutions\IPP\Data::charset",
                "document-charset-supported"                 => self::setof("EtdSolutions\IPP\Data::charset"),
                "document-creation-attributes-supported"     => self::setof("EtdSolutions\IPP\Data::keyword"),
                "document-digital-signature-default"         => "EtdSolutions\IPP\Data::keyword",
                "document-digital-signature-supported"       => self::setof("EtdSolutions\IPP\Data::keyword"),
                "document-format-default"                    => "EtdSolutions\IPP\Data::mimeMediaType",
                "document-format-details-default"            => self::collection("Operation", "document-format-details"),
                "document-format-details-supported"          => self::setof("EtdSolutions\IPP\Data::keyword"),
                "document-format-supported"                  => self::setof("EtdSolutions\IPP\Data::mimeMediaType"),
                "document-format-varying-attributes"         => self::setof("EtdSolutions\IPP\Data::keyword"),
                "document-format-version-default"            => self::text(127),
                "document-format-version-supported"          => self::setof(self::text(127)),
                "document-natural-language-default"          => "EtdSolutions\IPP\Data::naturalLanguage",
                "document-natural-language-supported"        => self::setof("EtdSolutions\IPP\Data::naturalLanguage"),
                "document-password-supported"                => self::integer(0, 1023),
                "feed-orientation-default"                   => "EtdSolutions\IPP\Data::keyword",
                "feed-orientation-supported"                 => "EtdSolutions\IPP\Data::keyword",
                "finishings-col-default"                     => self::collection("Job Template", "finishings-col"),
                "finishings-col-ready"                       => self::setof(self::collection("Job Template", "finishings-col")),
                "finishings-col-supported"                   => self::setof("EtdSolutions\IPP\Data::keyword"),
                "finishings-default"                         => self::setof("EtdSolutions\IPP\Data::enumeration"),
                "finishings-ready"                           => self::setof("EtdSolutions\IPP\Data::enumeration"),
                "finishings-supported"                       => self::setof("EtdSolutions\IPP\Data::enumeration"),
                "font-name-requested-default"                => "EtdSolutions\IPP\Data::name",
                "font-name-requested-supported"              => self::setof("EtdSolutions\IPP\Data::name"),
                "font-size-requested-default"                => self::integer(1, self::$MAX),
                "font-size-requested-supported"              => self::setof(self::rangeOfInteger(1, self::$MAX)),
                "force-front-side-default (under review)"    => self::setof(self::integer(1, self::$MAX)),
                "force-front-side-supported (under review)"  => self::rangeOfInteger(1, self::$MAX),
                "generated-natural-language-supported"       => self::setof("EtdSolutions\IPP\Data::naturalLanguage"),
                "identify-actions-default"                   => self::setof("EtdSolutions\IPP\Data::keyword"),
                "identify-actions-supported"                 => self::setof("EtdSolutions\IPP\Data::keyword"),
                "imposition-template-default"                => self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name"),
                "imposition-template-supported"              => self::setof(self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name")),
                "insert-after-page-number-supported"         => self::rangeOfInteger(0, self::$MAX),
                "insert-count-supported"                     => self::rangeOfInteger(0, self::$MAX),
                "insert-sheet-default"                       => self::setof(self::collection("Job Template", "insert-sheet")),
                "insert-sheet-supported"                     => self::setof("EtdSolutions\IPP\Data::keyword"),
                "ipp-features-supported"                     => self::setof("EtdSolutions\IPP\Data::keyword"),
                "ipp-versions-supported"                     => self::setof("EtdSolutions\IPP\Data::keyword"),
                "ippget-event-life"                          => self::integer(15, self::$MAX),
                "job-account-id-default"                     => self::underscore("EtdSolutions\IPP\Data::name", "EtdSolutions\IPP\Data::novalue"),
                "job-account-id-supported"                   => "EtdSolutions\IPP\Data::boolean",
                "job-accounting-sheets-default"              => self::underscore(self::collection("Job Template", "job-accounting-sheets"), "EtdSolutions\IPP\Data::novalue"),
                "job-accounting-sheets-supported"            => self::setof("EtdSolutions\IPP\Data::keyword"),
                "job-accounting-user-id-default"             => self::underscore("EtdSolutions\IPP\Data::name", "EtdSolutions\IPP\Data::novalue"),
                "job-accounting-user-id-supported"           => "EtdSolutions\IPP\Data::boolean",
                "job-constraints-supported"                  => self::setof("EtdSolutions\IPP\Data::collection"),
                "job-copies-default"                         => self::integer(1, self::$MAX),
                "job-copies-supported"                       => self::rangeOfInteger(1, self::$MAX),
                "job-cover-back-default"                     => self::collection("Job Template", "cover-back"),
                "job-cover-back-supported"                   => self::setof("EtdSolutions\IPP\Data::keyword"),
                "job-cover-front-default"                    => self::collection("Job Template", "cover-front"),
                "job-cover-front-supported"                  => self::setof("EtdSolutions\IPP\Data::keyword"),
                "job-creation-attributes-supported"          => self::setof("EtdSolutions\IPP\Data::keyword"),
                "job-delay-output-until-default"             => self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name"),
                "job-delay-output-until-supported"           => self::setof(self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name")),
                "job-delay-output-until-time-supported"      => self::rangeOfInteger(0, self::$MAX),
                "job-error-action-default"                   => "EtdSolutions\IPP\Data::keyword",
                "job-error-action-supported"                 => self::setof("EtdSolutions\IPP\Data::keyword"),
                "job-error-sheet-default"                    => self::underscore(self::collection("Job Template", "job-error-sheet"), "EtdSolutions\IPP\Data::novalue"),
                "job-error-sheet-supported"                  => self::setof("EtdSolutions\IPP\Data::keyword"),
                "job-finishings-col-default"                 => self::collection("Job Template", "finishings-col"),
                "job-finishings-col-ready"                   => self::setof(self::collection("Job Template", "finishings-col")),
                "job-finishings-col-supported"               => self::setof("EtdSolutions\IPP\Data::keyword"),
                "job-finishings-default"                     => self::setof("EtdSolutions\IPP\Data::enumeration"),
                "job-finishings-ready"                       => self::setof("EtdSolutions\IPP\Data::enumeration"),
                "job-finishings-supported"                   => self::setof("EtdSolutions\IPP\Data::enumeration"),
                "job-hold-until-default"                     => self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name"),
                "job-hold-until-supported"                   => self::setof(self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name")),
                "job-hold-until-time-supported"              => self::rangeOfInteger(0, self::$MAX),
                "job-ids-supported"                          => "EtdSolutions\IPP\Data::boolean",
                "job-impressions-supported"                  => self::rangeOfInteger(0, self::$MAX),
                "job-k-octets-supported"                     => self::rangeOfInteger(0, self::$MAX),
                "job-media-sheets-supported"                 => self::rangeOfInteger(0, self::$MAX),
                "job-message-to-operator-default"            => "EtdSolutions\IPP\Data::text",
                "job-message-to-operator-supported"          => "EtdSolutions\IPP\Data::boolean",
                "job-password-encryption-supported"          => self::setof(self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name")),
                "job-password-supported"                     => self::integer(0, 255),
                "job-phone-number-default"                   => self::underscore("EtdSolutions\IPP\Data::uri", "EtdSolutions\IPP\Data::novalue"),
                "job-phone-number-supported"                 => "EtdSolutions\IPP\Data::boolean",
                "job-priority-default"                       => self::integer(1, 100),
                "job-priority-supported"                     => self::integer(1, 100),
                "job-recipient-name-default"                 => self::underscore("EtdSolutions\IPP\Data::name", "EtdSolutions\IPP\Data::novalue"),
                "job-recipient-name-supported"               => "EtdSolutions\IPP\Data::boolean",
                "job-resolvers-supported"                    => self::setof(self::collection([
                    "resolver-name" => "EtdSolutions\IPP\Data::name"
                ])),
                "job-settable-attributes-supported"          => self::setof("EtdSolutions\IPP\Data::keyword"),
                "job-sheet-message-default"                  => "EtdSolutions\IPP\Data::text",
                "job-sheet-message-supported"                => "EtdSolutions\IPP\Data::boolean",
                "job-sheets-col-default"                     => self::collection("Job Template", "job-sheets-col"),
                "job-sheets-col-supported"                   => self::setof("EtdSolutions\IPP\Data::keyword"),
                "job-sheets-default"                         => self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name"),
                "job-sheets-supported"                       => self::setof(self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name")),
                "job-spooling-supported"                     => "EtdSolutions\IPP\Data::keyword",
                "max-save-info-supported"                    => self::integer(1, self::$MAX),
                "max-stitching-locations-supported"          => self::integer(1, self::$MAX),
                "media-back-coating-supported"               => self::setof(self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name")),
                "media-bottom-margin-supported"              => self::setof(self::integer(0, self::$MAX)),
                "media-col-database"                         => self::setof(self::collection([
                    //TODO=> Member attributes are the same as the
                    // "media-col" Job Template attribute
                    "media-source-properties" => self::collection([
                        "media-source-feed-direction"   => "EtdSolutions\IPP\Data::keyword",
                        "media-source-feed-orientation" => "EtdSolutions\IPP\Data::enumeration"
                    ])
                ])),
                "media-col-default"                          => self::collection("Job Template", "media-col"),
                "media-col-ready"                            => self::setof(self::collection([
                    //TODO=> Member attributes are the same as the
                    // "media-col" Job Template attribute
                    "media-source-properties" => self::collection([
                        "media-source-feed-direction"   => "EtdSolutions\IPP\Data::keyword",
                        "media-source-feed-orientation" => "EtdSolutions\IPP\Data::enumeration"
                    ])
                ])),
                "media-col-supported"                        => self::setof("EtdSolutions\IPP\Data::keyword"),
                "media-color-supported"                      => self::setof(self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name")),
                "media-default"                              => self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name", "EtdSolutions\IPP\Data::novalue"),
                "media-front-coating-supported"              => self::setof(self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name")),
                "media-grain-supported"                      => self::setof(self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name")),
                "media-hole-count-supported"                 => self::setof(self::rangeOfInteger(0, self::$MAX)),
                "media-info-supported"                       => "EtdSolutions\IPP\Data::boolean",
                "media-input-tray-check-default"             => self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name", "EtdSolutions\IPP\Data::novalue"),
                "media-input-tray-check-supported"           => self::setof(self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name")),
                "media-key-supported"                        => self::setof(self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name")),
                "media-left-margin-supported"                => self::setof(self::integer(0, self::$MAX)),
                "media-order-count-supported"                => self::setof(self::rangeOfInteger(1, self::$MAX)),
                "media-pre-printed-supported"                => self::setof(self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name")),
                "media-ready"                                => self::setof(self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name")),
                "media-recycled-supported"                   => self::setof(self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name")),
                "media-right-margin-supported"               => self::setof(self::integer(0, self::$MAX)),
                "media-size-supported"                       => self::setof(self::collection([
                    "x-dimension" => self::underscore(self::integer(1, self::$MAX), self::rangeOfInteger(1, self::$MAX)),
                    "y-dimension" => self::underscore(self::integer(1, self::$MAX), self::rangeOfInteger(1, self::$MAX))
                ])),
                "media-source-supported"                     => self::setof(self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name")),
                "media-supported"                            => self::setof(self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name")),
                "media-thickness-supported"                  => self::rangeOfInteger(1, self::$MAX),
                "media-tooth-supported"                      => self::setof(self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name")),
                "media-top-margin-supported"                 => self::setof(self::integer(0, self::$MAX)),
                "media-type-supported"                       => self::setof(self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name")),
                "media-weight-metric-supported"              => self::setof(self::rangeOfInteger(0, self::$MAX)),
                "multiple-document-handling-default"         => "EtdSolutions\IPP\Data::keyword",
                "multiple-document-handling-supported"       => self::setof("EtdSolutions\IPP\Data::keyword"),
                "multiple-document-jobs-supported"           => "EtdSolutions\IPP\Data::boolean",
                "multiple-operation-time-out"                => self::integer(1, self::$MAX),
                "multiple-operation-timeout-action"          => "EtdSolutions\IPP\Data::keyword",
                "natural-language-configured"                => "EtdSolutions\IPP\Data::naturalLanguage",
                "number-up-default"                          => self::integer(1, self::$MAX),
                "number-up-supported"                        => self::underscore(self::integer(1, self::$MAX), self::rangeOfInteger(1, self::$MAX)),
                "operations-supported"                       => self::setof("EtdSolutions\IPP\Data::enumeration"),
                "orientation-requested-default"              => self::underscore("EtdSolutions\IPP\Data::novalue", "EtdSolutions\IPP\Data::enumeration"),
                "orientation-requested-supported"            => self::setof("EtdSolutions\IPP\Data::enumeration"),
                "output-bin-default"                         => self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name"),
                "output-bin-supported"                       => self::setof(self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name")),
                "output-device-supported"                    => self::setof(self::name(127)),
                "overrides-supported"                        => self::setof("EtdSolutions\IPP\Data::keyword"),
                "page-delivery-default"                      => "EtdSolutions\IPP\Data::keyword",
                "page-delivery-supported"                    => self::setof("EtdSolutions\IPP\Data::keyword"),
                "page-order-received-default"                => "EtdSolutions\IPP\Data::keyword",
                "page-order-received-supported"              => self::setof("EtdSolutions\IPP\Data::keyword"),
                "page-ranges-supported"                      => "EtdSolutions\IPP\Data::boolean",
                "pages-per-minute"                           => self::integer(0, self::$MAX),
                "pages-per-minute-color"                     => self::integer(0, self::$MAX),
                "pages-per-subset-supported"                 => "EtdSolutions\IPP\Data::boolean",
                "parent-printers-supported"                  => self::setof("EtdSolutions\IPP\Data::uri"),
                "pdl-init-file-default"                      => self::underscore(self::collection("Job Template", "pdl-init-file"), "EtdSolutions\IPP\Data::novalue"),
                "pdl-init-file-entry-supported"              => self::setof("EtdSolutions\IPP\Data::name"),
                "pdl-init-file-location-supported"           => self::setof("EtdSolutions\IPP\Data::uri"),
                "pdl-init-file-name-subdirectory-supported"  => "EtdSolutions\IPP\Data::boolean",
                "pdl-init-file-name-supported"               => self::setof("EtdSolutions\IPP\Data::name"),
                "pdl-init-file-supported"                    => self::setof("EtdSolutions\IPP\Data::keyword"),
                "pdl-override-supported"                     => "EtdSolutions\IPP\Data::keyword",
                "preferred-attributes-supported"             => "EtdSolutions\IPP\Data::boolean",
                "presentation-direction-number-up-default"   => "EtdSolutions\IPP\Data::keyword",
                "presentation-direction-number-up-supported" => self::setof("EtdSolutions\IPP\Data::keyword"),
                "print-color-mode-default"                   => "EtdSolutions\IPP\Data::keyword",
                "print-color-mode-supported"                 => self::setof("EtdSolutions\IPP\Data::keyword"),
                "print-content-optimize-default"             => "EtdSolutions\IPP\Data::keyword",
                "print-content-optimize-supported"           => self::setof("EtdSolutions\IPP\Data::keyword"),
                "print-quality-default"                      => "EtdSolutions\IPP\Data::enumeration",
                "print-quality-supported"                    => self::setof("EtdSolutions\IPP\Data::enumeration"),
                "print-rendering-intent-default"             => "EtdSolutions\IPP\Data::keyword",
                "print-rendering-intent-supported"           => self::setof("EtdSolutions\IPP\Data::keyword"),
                "printer-alert"                              => self::setof("EtdSolutions\IPP\Data::octetString"),
                "printer-alert-description"                  => self::setof("EtdSolutions\IPP\Data::text"),
                "printer-charge-info"                        => "EtdSolutions\IPP\Data::text",
                "printer-charge-info-uri"                    => "EtdSolutions\IPP\Data::uri",
                "printer-current-time"                       => "EtdSolutions\IPP\Data::dateTime",
                "printer-detailed-status-messages"           => self::setof("EtdSolutions\IPP\Data::text"),
                "printer-device-id"                          => self::text(1023),
                "printer-driver-installer"                   => "EtdSolutions\IPP\Data::uri",
                "printer-geo-location"                       => "EtdSolutions\IPP\Data::uri",
                "printer-get-attributes-supported"           => self::setof("EtdSolutions\IPP\Data::keyword"),
                "printer-icc-profiles"                       => self::setof(self::collection([
                    "xri-authentication" => "EtdSolutions\IPP\Data::name",
                    "profile-url"        => "EtdSolutions\IPP\Data::uri"
                ])),
                "printer-icons"                              => self::setof("EtdSolutions\IPP\Data::uri"),
                "printer-info"                               => self::text(127),
                "printer-is-accepting-jobs"                  => "EtdSolutions\IPP\Data::boolean",
                "printer-location"                           => self::text(127),
                "printer-make-and-model"                     => self::text(127),
                "printer-mandatory-job-attributes"           => self::setof("EtdSolutions\IPP\Data::keyword"),
                "printer-message-date-time"                  => "EtdSolutions\IPP\Data::dateTime",
                "printer-message-from-operator"              => self::text(127),
                "printer-message-time"                       => "EtdSolutions\IPP\Data::integer",
                "printer-more-info"                          => "EtdSolutions\IPP\Data::uri",
                "printer-more-info-manufacturer"             => "EtdSolutions\IPP\Data::uri",
                "printer-name"                               => self::name(127),
                "printer-organization"                       => self::setof("EtdSolutions\IPP\Data::text"),
                "printer-organizational-unit"                => self::setof("EtdSolutions\IPP\Data::text"),
                "printer-resolution-default"                 => "EtdSolutions\IPP\Data::resolution",
                "printer-resolution-supported"               => "EtdSolutions\IPP\Data::resolution",
                "printer-settable-attributes-supported"      => self::setof("EtdSolutions\IPP\Data::keyword"),
                "printer-state"                              => "EtdSolutions\IPP\Data::enumeration",
                "printer-state-change-date-time"             => "EtdSolutions\IPP\Data::dateTime",
                "printer-state-change-time"                  => self::integer(1, self::$MAX),
                "printer-state-message"                      => "EtdSolutions\IPP\Data::text",
                "printer-state-reasons"                      => self::setof("EtdSolutions\IPP\Data::keyword"),
                "printer-supply"                             => self::setof("EtdSolutions\IPP\Data::octetString"),
                "printer-supply-description"                 => self::setof("EtdSolutions\IPP\Data::text"),
                "printer-supply-info-uri"                    => "EtdSolutions\IPP\Data::uri",
                "printer-up-time"                            => self::integer(1, self::$MAX),
                "printer-uri-supported"                      => self::setof("EtdSolutions\IPP\Data::uri"),
                "printer-uuid"                               => self::uri(45),
                "printer-xri-supported"                      => self::setof(self::collection([
                    "xri-authentication" => "EtdSolutions\IPP\Data::keyword",
                    "xri-security"       => "EtdSolutions\IPP\Data::keyword",
                    "xri-uri"            => "EtdSolutions\IPP\Data::uri"
                ])),
                "proof-print-default"                        => self::underscore(self::collection("Job Template", "proof-print"), "EtdSolutions\IPP\Data::novalue"),
                "proof-print-supported"                      => self::setof("EtdSolutions\IPP\Data::keyword"),
                "pwg-raster-document-resolution-supported"   => self::setof("EtdSolutions\IPP\Data::resolution"),
                "pwg-raster-document-sheet-back"             => "EtdSolutions\IPP\Data::keyword",
                "pwg-raster-document-type-supported"         => self::setof("EtdSolutions\IPP\Data::keyword"),
                "queued-job-count"                           => self::integer(0, self::$MAX),
                "reference-uri-schemes-supported"            => self::setof("EtdSolutions\IPP\Data::uriScheme"),
                "repertoire-supported"                       => self::setof(self::underscore("EtdSolutions\IPP\Data::keyword", "EtdSolutions\IPP\Data::name")),
                "requesting-user-uri-supported"              => "EtdSolutions\IPP\Data::boolean",
                "save-disposition-supported"                 => self::setof("EtdSolutions\IPP\Data::keyword"),
                "save-document-format-default"               => "EtdSolutions\IPP\Data::mimeMediaType",
                "save-document-format-supported"             => self::setof("EtdSolutions\IPP\Data::mimeMediaType"),
                "save-location-default"                      => "EtdSolutions\IPP\Data::uri",
                "save-location-supported"                    => self::setof("EtdSolutions\IPP\Data::uri"),
                "save-name-subdirectory-supported"           => "EtdSolutions\IPP\Data::boolean",
                "save-name-supported"                        => "EtdSolutions\IPP\Data::boolean",
                "separator-sheets-default"                   => self::collection("Job Template", "separator-sheets"),
                "separator-sheets-supported"                 => self::setof("EtdSolutions\IPP\Data::keyword"),
                "sheet-collate-default"                      => "EtdSolutions\IPP\Data::keyword",
                "sheet-collate-supported"                    => self::setof("EtdSolutions\IPP\Data::keyword"),
                "sides-default"                              => "EtdSolutions\IPP\Data::keyword",
                "sides-supported"                            => self::setof("EtdSolutions\IPP\Data::keyword"),
                "stitching-locations-supported"              => self::setof(self::underscore(self::integer(0, self::$MAX), self::rangeOfInteger(0, self::$MAX))),
                "stitching-offset-supported"                 => self::setof(self::underscore(self::integer(0, self::$MAX), self::rangeOfInteger(0, self::$MAX))),
                "subordinate-printers-supported"             => self::setof("EtdSolutions\IPP\Data::uri"),
                "uri-authentication-supported"               => self::setof("EtdSolutions\IPP\Data::keyword"),
                "uri-security-supported"                     => self::setof("EtdSolutions\IPP\Data::keyword"),
                "user-defined-values-supported"              => self::setof("EtdSolutions\IPP\Data::keyword"),
                "which-jobs-supported"                       => self::setof("EtdSolutions\IPP\Data::keyword"),
                "x-image-position-default"                   => "EtdSolutions\IPP\Data::keyword",
                "x-image-position-supported"                 => self::setof("EtdSolutions\IPP\Data::keyword"),
                "x-image-shift-default"                      => "EtdSolutions\IPP\Data::integer",
                "x-image-shift-supported"                    => "EtdSolutions\IPP\Data::rangeOfInteger",
                "x-side1-image-shift-default"                => "EtdSolutions\IPP\Data::integer",
                "x-side1-image-shift-supported"              => "EtdSolutions\IPP\Data::rangeOfInteger",
                "x-side2-image-shift-default"                => "EtdSolutions\IPP\Data::integer",
                "x-side2-image-shift-supported"              => "EtdSolutions\IPP\Data::rangeOfInteger",
                "xri-authentication-supported"               => self::setof("EtdSolutions\IPP\Data::keyword"),
                "xri-security-supported"                     => self::setof("EtdSolutions\IPP\Data::keyword"),
                "xri-uri-scheme-supported"                   => self::setof("EtdSolutions\IPP\Data::uriScheme"),
                "y-image-position-default"                   => "EtdSolutions\IPP\Data::keyword",
                "y-image-position-supported"                 => self::setof("EtdSolutions\IPP\Data::keyword"),
                "y-image-shift-default"                      => "EtdSolutions\IPP\Data::integer",
                "y-image-shift-supported"                    => "EtdSolutions\IPP\Data::rangeOfInteger",
                "y-side1-image-shift-default"                => "EtdSolutions\IPP\Data::integer",
                "y-side1-image-shift-supported"              => "EtdSolutions\IPP\Data::rangeOfInteger",
                "y-side2-image-shift-default"                => "EtdSolutions\IPP\Data::integer",
                "y-side2-image-shift-supported"              => "EtdSolutions\IPP\Data::rangeOfInteger"
            ],
            "Subscription Description" => [
                "notify-job-id"                => self::integer(1, self::$MAX),
                "notify-lease-expiration-time" => self::integer(0, self::$MAX),
                "notify-printer-up-time"       => self::integer(1, self::$MAX),
                "notify-printer-uri"           => "EtdSolutions\IPP\Data::uri",
                "notify-sequence-number"       => self::integer(0, self::$MAX),
                "notify-subscriber-user-name"  => "EtdSolutions\IPP\Data::name",
                "notify-subscriber-user-uri"   => "EtdSolutions\IPP\Data::uri",
                "notify-subscription-id"       => self::integer(1, self::$MAX),
                "subscription-uuid"            => "EtdSolutions\IPP\Data::uri"
            ],
            "Subscription Template"    => [
                "notify-attributes"               => self::setof("EtdSolutions\IPP\Data::keyword"),
                "notify-attributes-supported"     => self::setof("EtdSolutions\IPP\Data::keyword"),
                "notify-charset"                  => "EtdSolutions\IPP\Data::charset",
                "notify-events"                   => self::setof("EtdSolutions\IPP\Data::keyword"),
                "notify-events-default"           => self::setof("EtdSolutions\IPP\Data::keyword"),
                "notify-events-supported"         => self::setof("EtdSolutions\IPP\Data::keyword"),
                "notify-lease-duration"           => self::integer(0, 67108863),
                "notify-lease-duration-default"   => self::integer(0, 67108863),
                "notify-lease-duration-supported" => self::setof(self::underscore(self::integer(0, 67108863), self::rangeOfInteger(0, 67108863))),
                "notify-max-events-supported"     => self::integer(2, self::$MAX),
                "notify-natural-language"         => "EtdSolutions\IPP\Data::naturalLanguage",
                "notify-pull-method"              => "EtdSolutions\IPP\Data::keyword",
                "notify-pull-method-supported"    => self::setof("EtdSolutions\IPP\Data::keyword"),
                "notify-recipient-uri"            => "EtdSolutions\IPP\Data::uri",
                "notify-schemes-supported"        => self::setof("EtdSolutions\IPP\Data::uriScheme"),
                "notify-time-interval"            => self::integer(0, self::$MAX),
                "notify-user-data"                => self::octetString(63)
            ]
        ];

        $attributes = self::resolve($attributes);

        if (!isset(self::$data)) {
            self::$data = [];
        }

        self::$data["attributes"] = $attributes;

    }

    protected static function seedKeywords() {

        $attributes = self::get("attributes");

        // Keywords
        // ------------

        $keywords = [];

        //media is different from the others because it has sub-groups
        $media = [
            "size name"     => [
                "a",
                "arch-a",
                "arch-b",
                "arch-c",
                "arch-d",
                "arch-e",
                "asme_f_28x40in",
                "b",
                "c",
                "choice_iso_a4_210x297mm_na_letter_8.5x11in",
                "d",
                "e",
                "executive",
                "f",
                "folio",
                "invoice",
                "iso-a0",
                "iso-a1",
                "iso-a2",
                "iso-a3",
                "iso-a4",
                "iso-a5",
                "iso-a6",
                "iso-a7",
                "iso-a8",
                "iso-a9",
                "iso-a10",
                "iso-b0",
                "iso-b1",
                "iso-b2",
                "iso-b3",
                "iso-b4",
                "iso-b5",
                "iso-b6",
                "iso-b7",
                "iso-b8",
                "iso-b9",
                "iso-b10",
                "iso-c3",
                "iso-c4",
                "iso-c5",
                "iso-c6",
                "iso-designated-long",
                "iso_2a0_1189x1682mm",
                "iso_a0_841x1189mm",
                "iso_a1_594x841mm",
                "iso_a1x3_841x1783mm",
                "iso_a1x4_841x2378mm",
                "iso_a2_420x594mm",
                "iso_a2x3_594x1261mm",
                "iso_a2x4_594x1682mm",
                "iso_a2x5_594x2102mm",
                "iso_a3-extra_322x445mm",
                "iso_a3_297x420mm",
                "iso_a0x3_1189x2523mm",
                "iso_a3x3_420x891mm",
                "iso_a3x4_420x1189mm",
                "iso_a3x5_420x1486mm",
                "iso_a3x6_420x1783mm",
                "iso_a3x7_420x2080mm",
                "iso_a4-extra_235.5x322.3mm",
                "iso_a4-tab_225x297mm",
                "iso_a4_210x297mm",
                "iso_a4x3_297x630mm",
                "iso_a4x4_297x841mm",
                "iso_a4x5_297x1051mm",
                "iso_a4x6_297x1261mm",
                "iso_a4x7_297x1471mm",
                "iso_a4x8_297x1682mm",
                "iso_a4x9_297x1892mm",
                "iso_a5-extra_174x235mm",
                "iso_a5_148x210mm",
                "iso_a6_105x148mm",
                "iso_a7_74x105mm",
                "iso_a8_52x74mm",
                "iso_a9_37x52mm",
                "iso_a10_26x37mm",
                "iso_b0_1000x1414mm",
                "iso_b1_707x1000mm",
                "iso_b2_500x707mm",
                "iso_b3_353x500mm",
                "iso_b4_250x353mm",
                "iso_b5-extra_201x276mm",
                "iso_b5_176x250mm",
                "iso_b6_125x176mm",
                "iso_b6c4_125x324mm",
                "iso_b7_88x125mm",
                "iso_b8_62x88mm",
                "iso_b9_44x62mm",
                "iso_b10_31x44mm",
                "iso_c0_917x1297mm",
                "iso_c1_648x917mm",
                "iso_c2_458x648mm",
                "iso_c3_324x458mm",
                "iso_c4_229x324mm",
                "iso_c5_162x229mm",
                "iso_c6_114x162mm",
                "iso_c6c5_114x229mm",
                "iso_c7_81x114mm",
                "iso_c7c6_81x162mm",
                "iso_c8_57x81mm",
                "iso_c9_40x57mm",
                "iso_c10_28x40mm",
                "iso_dl_110x220mm",
                "iso_ra0_860x1220mm",
                "iso_ra1_610x860mm",
                "iso_ra2_430x610mm",
                "iso_sra0_900x1280mm",
                "iso_sra1_640x900mm",
                "iso_sra2_450x640mm",
                "jis-b0",
                "jis-b1",
                "jis-b2",
                "jis-b3",
                "jis-b4",
                "jis-b5",
                "jis-b6",
                "jis-b7",
                "jis-b8",
                "jis-b9",
                "jis-b10",
                "jis_b0_1030x1456mm",
                "jis_b1_728x1030mm",
                "jis_b2_515x728mm",
                "jis_b3_364x515mm",
                "jis_b4_257x364mm",
                "jis_b5_182x257mm",
                "jis_b6_128x182mm",
                "jis_b7_91x128mm",
                "jis_b8_64x91mm",
                "jis_b9_45x64mm",
                "jis_b10_32x45mm",
                "jis_exec_216x330mm",
                "jpn_chou2_111.1x146mm",
                "jpn_chou3_120x235mm",
                "jpn_chou4_90x205mm",
                "jpn_hagaki_100x148mm",
                "jpn_kahu_240x322.1mm",
                "jpn_kaku2_240x332mm",
                "jpn_oufuku_148x200mm",
                "jpn_you4_105x235mm",
                "ledger",
                "monarch",
                "na-5x7",
                "na-6x9",
                "na-7x9",
                "na-8x10",
                "na-9x11",
                "na-9x12",
                "na-10x13",
                "na-10x14",
                "na-10x15",
                "na-legal",
                "na-letter",
                "na-number-9",
                "na-number-10",
                "na_5x7_5x7in",
                "na_6x9_6x9in",
                "na_7x9_7x9in",
                "na_9x11_9x11in",
                "na_10x11_10x11in",
                "na_10x13_10x13in",
                "na_10x14_10x14in",
                "na_10x15_10x15in",
                "na_11x12_11x12in",
                "na_11x15_11x15in",
                "na_12x19_12x19in",
                "na_a2_4.375x5.75in",
                "na_arch-a_9x12in",
                "na_arch-b_12x18in",
                "na_arch-c_18x24in",
                "na_arch-d_24x36in",
                "na_arch-e_36x48in",
                "na_b-plus_12x19.17in",
                "na_c5_6.5x9.5in",
                "na_c_17x22in",
                "na_d_22x34in",
                "na_e_34x44in",
                "na_edp_11x14in",
                "na_eur-edp_12x14in",
                "na_executive_7.25x10.5in",
                "na_f_44x68in",
                "na_fanfold-eur_8.5x12in",
                "na_fanfold-us_11x14.875in",
                "na_foolscap_8.5x13in",
                "na_govt-legal_8x13in",
                "na_govt-letter_8x10in",
                "na_index-3x5_3x5in",
                "na_index-4x6-ext_6x8in",
                "na_index-4x6_4x6in",
                "na_index-5x8_5x8in",
                "na_invoice_5.5x8.5in",
                "na_ledger_11x17in",
                "na_legal-extra_9.5x15in",
                "na_legal_8.5x14in",
                "na_letter-extra_9.5x12in",
                "na_letter-plus_8.5x12.69in",
                "na_letter_8.5x11in",
                "na_monarch_3.875x7.5in",
                "na_number-9_3.875x8.875in",
                "na_number-10_4.125x9.5in",
                "na_number-11_4.5x10.375in",
                "na_number-12_4.75x11in",
                "na_number-14_5x11.5in",
                "na_personal_3.625x6.5in",
                "na_quarto_8.5x10.83in",
                "na_super-a_8.94x14in",
                "na_super-b_13x19in",
                "na_wide-format_30x42in",
                "om_dai-pa-kai_275x395mm",
                "om_folio-sp_215x315mm",
                "om_folio_210x330mm",
                "om_invite_220x220mm",
                "om_italian_110x230mm",
                "om_juuro-ku-kai_198x275mm",
                "om_large-photo_200x300",
                "om_pa-kai_267x389mm",
                "om_postfix_114x229mm",
                "om_small-photo_100x150mm",
                "prc_1_102x165mm",
                "prc_2_102x176mm",
                "prc_3_125x176mm",
                "prc_4_110x208mm",
                "prc_5_110x220mm",
                "prc_6_120x320mm",
                "prc_7_160x230mm",
                "prc_8_120x309mm",
                "prc_10_324x458mm",
                "prc_16k_146x215mm",
                "prc_32k_97x151mm",
                "quarto",
                "roc_8k_10.75x15.5in",
                "roc_16k_7.75x10.75in",
                "super-b",
                "tabloid"
            ],
            "media name"    => [
                "a-translucent",
                "a-transparent",
                "a-white",
                "arch-a-translucent",
                "arch-a-transparent",
                "arch-a-white",
                "arch-axsynchro-translucent",
                "arch-axsynchro-transparent",
                "arch-axsynchro-white",
                "arch-b-translucent",
                "arch-b-transparent",
                "arch-b-white",
                "arch-bxsynchro-translucent",
                "arch-bxsynchro-transparent",
                "arch-bxsynchro-white",
                "arch-c-translucent",
                "arch-c-transparent",
                "arch-c-white",
                "arch-cxsynchro-translucent",
                "arch-cxsynchro-transparent",
                "arch-cxsynchro-white",
                "arch-d-translucent",
                "arch-d-transparent",
                "arch-d-white",
                "arch-dxsynchro-translucent",
                "arch-dxsynchro-transparent",
                "arch-dxsynchro-white",
                "arch-e-translucent",
                "arch-e-transparent",
                "arch-e-white",
                "arch-exsynchro-translucent",
                "arch-exsynchro-transparent",
                "arch-exsynchro-white",
                "auto-fixed-size-translucent",
                "auto-fixed-size-transparent",
                "auto-fixed-size-white",
                "auto-synchro-translucent",
                "auto-synchro-transparent",
                "auto-synchro-white",
                "auto-translucent",
                "auto-transparent",
                "auto-white",
                "axsynchro-translucent",
                "axsynchro-transparent",
                "axsynchro-white",
                "b-translucent",
                "b-transparent",
                "b-white",
                "bxsynchro-translucent",
                "bxsynchro-transparent",
                "bxsynchro-white",
                "c-translucent",
                "c-transparent",
                "c-white",
                "custom1",
                "custom2",
                "custom3",
                "custom4",
                "custom5",
                "custom6",
                "custom7",
                "custom8",
                "custom9",
                "custom10",
                "cxsynchro-translucent",
                "cxsynchro-transparent",
                "cxsynchro-white",
                "d-translucent",
                "d-transparent",
                "d-white",
                "default",
                "dxsynchro-translucent",
                "dxsynchro-transparent",
                "dxsynchro-white",
                "e-translucent",
                "e-transparent",
                "e-white",
                "executive-white",
                "exsynchro-translucent",
                "exsynchro-transparent",
                "exsynchro-white",
                "folio-white",
                "invoice-white",
                "iso-a0-translucent",
                "iso-a0-transparent",
                "iso-a0-white",
                "iso-a0xsynchro-translucent",
                "iso-a0xsynchro-transparent",
                "iso-a0xsynchro-white",
                "iso-a1-translucent",
                "iso-a1-transparent",
                "iso-a1-white",
                "iso-a1x3-translucent",
                "iso-a1x3-transparent",
                "iso-a1x3-white",
                "iso-a1x4- translucent",
                "iso-a1x4-transparent",
                "iso-a1x4-white",
                "iso-a1xsynchro-translucent",
                "iso-a1xsynchro-transparent",
                "iso-a1xsynchro-white",
                "iso-a2-translucent",
                "iso-a2-transparent",
                "iso-a2-white",
                "iso-a2x3-translucent",
                "iso-a2x3-transparent",
                "iso-a2x3-white",
                "iso-a2x4-translucent",
                "iso-a2x4-transparent",
                "iso-a2x4-white",
                "iso-a2x5-translucent",
                "iso-a2x5-transparent",
                "iso-a2x5-white",
                "iso-a2xsynchro-translucent",
                "iso-a2xsynchro-transparent",
                "iso-a2xsynchro-white",
                "iso-a3-colored",
                "iso-a3-translucent",
                "iso-a3-transparent",
                "iso-a3-white",
                "iso-a3x3-translucent",
                "iso-a3x3-transparent",
                "iso-a3x3-white",
                "iso-a3x4-translucent",
                "iso-a3x4-transparent",
                "iso-a3x4-white",
                "iso-a3x5-translucent",
                "iso-a3x5-transparent",
                "iso-a3x5-white",
                "iso-a3x6-translucent",
                "iso-a3x6-transparent",
                "iso-a3x6-white",
                "iso-a3x7-translucent",
                "iso-a3x7-transparent",
                "iso-a3x7-white",
                "iso-a3xsynchro-translucent",
                "iso-a3xsynchro-transparent",
                "iso-a3xsynchro-white",
                "iso-a4-colored",
                "iso-a4-translucent",
                "iso-a4-transparent",
                "iso-a4-white",
                "iso-a4x3-translucent",
                "iso-a4x3-transparent",
                "iso-a4x3-white",
                "iso-a4x4-translucent",
                "iso-a4x4-transparent",
                "iso-a4x4-white",
                "iso-a4x5-translucent",
                "iso-a4x5-transparent",
                "iso-a4x5-white",
                "iso-a4x6-translucent",
                "iso-a4x6-transparent",
                "iso-a4x6-white",
                "iso-a4x7-translucent",
                "iso-a4x7-transparent",
                "iso-a4x7-white",
                "iso-a4x8-translucent",
                "iso-a4x8-transparent",
                "iso-a4x8-white",
                "iso-a4x9-translucent",
                "iso-a4x9-transparent",
                "iso-a4x9-white",
                "iso-a4xsynchro-translucent",
                "iso-a4xsynchro-transparent",
                "iso-a4xsynchro-white",
                "iso-a5-colored",
                "iso-a5-translucent",
                "iso-a5-transparent",
                "iso-a5-white",
                "iso-a6-white",
                "iso-a7-white",
                "iso-a8-white",
                "iso-a9-white",
                "iso-a10-white",
                "iso-b0-white",
                "iso-b1-white",
                "iso-b2-white",
                "iso-b3-white",
                "iso-b4-colored",
                "iso-b4-white",
                "iso-b5-colored",
                "iso-b5-white",
                "iso-b6-white",
                "iso-b7-white",
                "iso-b8-white",
                "iso-b9-white",
                "iso-b10-white",
                "jis-b0-translucent",
                "jis-b0-transparent",
                "jis-b0-white",
                "jis-b1-translucent",
                "jis-b1-transparent",
                "jis-b1-white",
                "jis-b2-translucent",
                "jis-b2-transparent",
                "jis-b2-white",
                "jis-b3-translucent",
                "jis-b3-transparent",
                "jis-b3-white",
                "jis-b4-colored",
                "jis-b4-translucent",
                "jis-b4-transparent",
                "jis-b4-white",
                "jis-b5-colored",
                "jis-b5-translucent",
                "jis-b5-transparent",
                "jis-b5-white",
                "jis-b6-white",
                "jis-b7-white",
                "jis-b8-white",
                "jis-b9-white",
                "jis-b10-white",
                "ledger-white",
                "na-legal-colored",
                "na-legal-white",
                "na-letter-colored",
                "na-letter-transparent",
                "na-letter-white",
                "quarto-white"
            ],
            "media type"    => [
                "bond",
                "heavyweight",
                "labels",
                "letterhead",
                "plain",
                "pre-printed",
                "pre-punched",
                "recycled",
                "transparency"
            ],
            "input tray"    => [
                "bottom",
                "by-pass-tray",
                "envelope",
                "large-capacity",
                "main",
                "manual",
                "middle",
                "side",
                "top",
                "tray-1",
                "tray-2",
                "tray-3",
                "tray-4",
                "tray-5",
                "tray-6",
                "tray-7",
                "tray-8",
                "tray-9",
                "tray-10"
            ],
            "envelope name" => [
                "iso-b4-envelope",
                "iso-b5-envelope",
                "iso-c3-envelope",
                "iso-c4-envelope",
                "iso-c5-envelope",
                "iso-c6-envelope",
                "iso-designated-long-envelope",
                "monarch-envelope",
                "na-6x9-envelope",
                "na-7x9-envelope",
                "na-9x11-envelope",
                "na-9x12-envelope",
                "na-10x13-envelope",
                "na-10x14-envelope",
                "na-10x15-envelope",
                "na-number-9-envelope",
                "na-number-10-envelope"
            ]
        ];

        $Job_Template_attribute_names               = array_keys($attributes["Job Template"]);
        $Job_Template_and_Operation_attribute_names = array_merge($Job_Template_attribute_names, array_keys($attributes["Operation"]));
        $Printer_attribute_names                    = array_merge(array_keys($attributes["Job Template"]), ["none"]);
        $media_name_or_size                         = array_merge($media["media name"], $media["size name"]);

        $keywords["compression"]                                = keyword([
            "compress",
            "deflate",
            "gzip",
            "none"
        ]);
        $keywords["compression-supported"]                      = setof_keyword(
            $keywords["compression"]
        );
        $keywords["cover-back-supported"]                       = setof_keyword([
            "cover-type",
            "media",
            "media-col"
        ]);
        $keywords["cover-front-supported"]                      = setof_keyword(
            $keywords["cover-back-supported"]
        );
        $keywords["cover-type"]                                 = keyword([
            "no-cover",
            "print-back",
            "print-both",
            "print-front",
            "print-none"
        ]);
        $keywords["document-digital-signature"]                 = keyword([
            "dss",
            "none",
            "pgp",
            "smime",
            "xmldsig"
        ]);
        $keywords["document-digital-signature-default"]         = keyword(
            $keywords["document-digital-signature"]
        );
        $keywords["document-digital-signature-supported"]       = setof_keyword(
            $keywords["document-digital-signature"]
        );
        $keywords["document-format-details-supported"]          = setof_keyword([
            "document-format",
            "document-format-device-id",
            "document-format-version",
            "document-natural-language",
            "document-source-application-name",
            "document-source-application-version",
            "document-source-os-name",
            "document-source-os-version"
        ]);
        $keywords["document-format-varying-attributes"]         = setof_keyword(
//Any Printer attribute keyword name
            $Printer_attribute_names
        );
        $keywords["document-state-reasons"]                     = setof_keyword([
            "aborted-by-system",
            "canceled-at-device",
            "canceled-by-operator",
            "canceled-by-user",
            "completed-successfully",
            "completed-with-errors",
            "completed-with-warnings",
            "compression-error",
            "data-insufficient",
            "digital-signature-did-not-verify",
            "digital-signature-type-not-supported",
            "digital-signature-wait",
            "document-access-error",
            "document-format-error",
            "document-password-error",
            "document-permission-error",
            "document-security-error",
            "document-unprintable-error",
            "errors-detected",
            "incoming",
            "interpreting",
            "none",
            "outgoing",
            "printing",
            "processing-to-stop-point",
            "queued",
            "queued-for-marker",
            "queued-in-device",
            "resources-are-not-ready",
            "resources-are-not-supported",
            "submission-interrupted",
            "transforming",
            "unsupported-compression",
            "unsupported-document-format",
            "warnings-detected"
        ]);
        $keywords["feed-orientation"]                           = keyword([
            "long-edge-first",
            "short-edge-first"
        ]);
        $keywords["feed-orientation-supported"]                 = setof_keyword(
            $keywords["feed-orientation"]
        );
        $keywords["finishings-col-supported"]                   = setof_keyword([
            "finishing-template",
            "stitching"
        ]);
        $keywords["identify-actions"]                           = setof_keyword([
            "display",
            "flash",
            "sound",
            "speak"
        ]);
        $keywords["identify-actions-default"]                   = setof_keyword(
            $keywords["identify-actions"]
        );
        $keywords["identify-actions-supported"]                 = setof_keyword(
            $keywords["identify-actions"]
        );
        $keywords["imposition-template"]                        = keyword_name([
            "none",
            "signature"
        ]);
        $keywords["ipp-features-supported"]                     = setof_keyword([
            "document-object",
            "ipp-everywhere",
            "job-save",
            "none",
            "page-overrides",
            "proof-print",
            "subscription-object"
        ]);
        $keywords["ipp-versions-supported"]                     = setof_keyword([
            "1.0",
            "1.1",
            "2.0",
            "2.1",
            "2.2"
        ]);
        $keywords["job-accounting-sheets-type"]                 = keyword_name([
            "none",
            "standard"
        ]);
        $keywords["job-cover-back-supported"]                   = setof_keyword(
            $keywords["cover-back-supported"]
        );
        $keywords["job-cover-front-supported"]                  = setof_keyword(
            $keywords["cover-front-supported"]
        );
        $keywords["job-creation-attributes-supported"]          = setof_keyword(
//  Any Job Template attribute
//  Any job creation Operation attribute keyword name
            $Job_Template_and_Operation_attribute_names
        );
        $keywords["job-error-action"]                           = keyword([
            "abort-job",
            "cancel-job",
            "continue-job",
            "suspend-job"
        ]);
        $keywords["job-error-action-default"]                   = keyword(
            $keywords["job-error-action"]
        );
        $keywords["job-error-action-supported"]                 = setof_keyword(
            $keywords["job-error-action"]
        );
        $keywords["job-error-sheet-type"]                       = keyword_name([
            "none",
            "standard"
        ]);
        $keywords["job-error-sheet-when"]                       = keyword([
            "always",
            "on-error"
        ]);
        $keywords["job-finishings-col-supported"]               = setof_keyword(
            $keywords["finishings-col-supported"]
        );
        $keywords["job-hold-until"]                             = keyword_name([
            "day-time",
            "evening",
            "indefinite",
            "night",
            "no-hold",
            "second-shift",
            "third-shift",
            "weekend"
        ]);
        $keywords["job-hold-until-default"]                     = keyword_name(
            $keywords["job-hold-until"]
        );
        $keywords["job-hold-until-supported"]                   = setof_keyword_name(
            $keywords["job-hold-until"]
        );
        $keywords["job-mandatory-attributes"]                   = setof_keyword(
//  Any Job Template attribute
            $Job_Template_attribute_names
        );
        $keywords["job-password-encryption"]                    = keyword_name([
            "md2",
            "md4",
            "md5",
            "none",
            "sha"
        ]);
        $keywords["job-password-encryption-supported"]          = setof_keyword_name(
            $keywords["job-password-encryption"]
        );
        $keywords["job-save-disposition-supported"]             = setof_keyword([
            "save-disposition",
            "save-info"
        ]);
        $keywords["job-settable-attributes-supported"]          = setof_keyword(
//  Any Job Template attribute
            $Job_Template_attribute_names
        );
        $keywords["job-sheets"]                                  = keyword_name([
            "first-print-stream-page",
            "job-both-sheet",
            "job-end-sheet",
            "job-start-sheet",
            "none",
            "standard"
        ]);
        $keywords["job-sheets-default"]                         = keyword_name(
            $keywords["job-sheets"]
        );
        $keywords["job-sheets-supported"]                       = setof_keyword_name(
            $keywords["job-sheets"]
        );
        $keywords["job-spooling-supported"]                     = keyword([
            "automatic",
            "spool",
            "stream"
        ]);
        $keywords["job-state-reasons"]                          = setof_keyword([
            "aborted-by-system",
            "compression-error",
            "digital-signature-did-not-verify",
            "digital-signature-type-not-supported",
            "document-access-error",
            "document-format-error",
            "document-password-error",
            "document-permission-error",
            "document-security-error",
            "document-unprintable-error",
            "errors-detected",
            "job-canceled-at-device",
            "job-canceled-by-operator",
            "job-canceled-by-user",
            "job-completed-successfully",
            "job-completed-with-errors",
            "job-completed-with-warnings",
            "job-data-insufficient",
            "job-delay-output-until-specified",
            "job-digital-signature-wait",
            "job-hold-until-specified",
            "job-incoming",
            "job-interpreting",
            "job-outgoing",
            "job-password-wait",
            "job-printed-successfully",
            "job-printed-with-errors",
            "job-printed-with-warnings",
            "job-printing",
            "job-queued",
            "job-queued-for-marker",
            "job-restartable",
            "job-resuming",
            "job-saved-successfully",
            "job-saved-with-errors",
            "job-saved-with-warnings",
            "job-saving",
            "job-spooling",
            "job-streaming",
            "job-suspended",
            "job-suspended-by-operator",
            "job-suspended-by-system",
            "job-suspended-by-user",
            "job-suspending",
            "job-transforming",
            "none",
            "printer-stopped",
            "printer-stopped-partly",
            "processing-to-stop-point",
            "queued-in-device",
            "resources-are-not-ready",
            "resources-are-not-supported",
            "service-off-line",
            "submission-interrupted",
            "unsupported-compression",
            "unsupported-document-format",
            "warnings-detected"
        ]);
        $keywords["media"]                                      = keyword_name(
            array_merge($media["size name"], $media["media name"], $media["media type"], $media["input tray"], $media["envelope name"])
        );
        $keywords["media-back-coating"]                         = keyword_name([
            "glossy",
            "high-gloss",
            "matte",
            "none",
            "satin",
            "semi-gloss"
        ]);
        $keywords["media-back-coating-supported"]               = setof_keyword_name(
            $keywords["media-back-coating"]
        );
        $keywords["media-col-supported"]                        = setof_keyword([
            "media-bottom-margin",
            "media-left-margin",
            "media-right-margin",
            "media-size-name",
            "media-source",
            "media-top-margin"
        ]);
        $keywords["media-color"]                                = keyword_name([
            "blue",
            "buff",
            "goldenrod",
            "gray",
            "green",
            "ivory",
            "no-color",
            "orange",
            "pink",
            "red",
            "white",
            "yellow"
        ]);
        $keywords["media-color-supported"]                      = setof_keyword_name(
            $keywords["media-color"]
        );
        $keywords["media-default"]                              = keyword_name_novalue(
            $keywords["media"]
        );
        $keywords["media-front-coating"]                        = keyword_name(
            $keywords["media-back-coating"]
        );
        $keywords["media-front-coating-supported"]              = setof_keyword_name(
            $keywords["media-back-coating"]
        );
        $keywords["media-grain"]                                = keyword_name([
            "x-direction",
            "y-direction"
        ]);
        $keywords["media-grain-supported"]                      = setof_keyword_name(
            $keywords["media-grain"]
        );
        $keywords["media-input-tray-check"]                     = keyword_name([
            $media["input tray"]
        ]);
        $keywords["media-input-tray-check-default"]             = keyword_name([
            $media["input tray"]
        ]);
        $keywords["media-input-tray-check-supported"]           = setof_keyword_name(
            $media["input tray"]
        );
        $keywords["media-key"]                                  = keyword_name(
//  Any "media" media or size keyword value
            $media_name_or_size
        );
        $keywords["media-key-supported"]                        = setof_keyword_name([
//  Any "media" media or size keyword value
            $media_name_or_size
        ]);
        $keywords["media-pre-printed"]                          = keyword_name([
            "blank",
            "letter-head",
            "pre-printed"
        ]);
        $keywords["media-pre-printed-supported"]                = keyword_name(
            $keywords["media-pre-printed"]
        );
        $keywords["media-ready"]                                = setof_keyword_name([
//  Any "media" media or size keyword value
            $media_name_or_size
        ]);
        $keywords["media-recycled"]                             = keyword_name([
            "none",
            "standard"
        ]);
        $keywords["media-recycled-supported"]                   = keyword_name(
            $keywords["media-recycled"]
        );
        $keywords["media-source"]                               = keyword_name([
            "alternate",
            "alternate-roll",
            "auto",
            "bottom",
            "by-pass-tray",
            "center",
            "disc",
            "envelope",
            "hagaki",
            "large-capacity",
            "left",
            "main",
            "main-roll",
            "manual",
            "middle",
            "photo",
            "rear",
            "right",
            "roll-1",
            "roll-2",
            "roll-3",
            "roll-4",
            "roll-5",
            "roll-6",
            "roll-7",
            "roll-8",
            "roll-9",
            "roll-10",
            "side",
            "top",
            "tray-1",
            "tray-2",
            "tray-3",
            "tray-4",
            "tray-5",
            "tray-6",
            "tray-7",
            "tray-8",
            "tray-9",
            "tray-10",
            "tray-11",
            "tray-12",
            "tray-13",
            "tray-14",
            "tray-15",
            "tray-16",
            "tray-17",
            "tray-18",
            "tray-19",
            "tray-20"
        ]);
        $keywords["media-source-feed-direction"]                = keyword(
            $keywords["feed-orientation"]
        );
        $keywords["media-source-supported"]                     = setof_keyword_name(
            $keywords["media-source"]
        );
        $keywords["media-supported"]                            = setof_keyword_name(
            $keywords["media"]
        );
        $keywords["media-tooth"]                                = keyword_name([
            "antique",
            "calendared",
            "coarse",
            "fine",
            "linen",
            "medium",
            "smooth",
            "stipple",
            "uncalendared",
            "vellum"
        ]);
        $keywords["media-tooth-supported"]                      = setof_keyword_name(
            $keywords["media-tooth"]
        );
        $keywords["media-type"]                                 = keyword_name([
            "aluminum",
            "back-print-film",
            "cardboard",
            "cardstock",
            "cd",
            "continuous",
            "continuous-long",
            "continuous-short",
            "corrugated-board",
            "disc",
            "double-wall",
            "dry-film",
            "dvd",
            "embossing-foil",
            "end-board",
            "envelope",
            "envelope-plain",
            "envelope-window",
            "film",
            "flexo-base",
            "flexo-photo-polymer",
            "flute",
            "foil",
            "full-cut-tabs",
            "gravure-cylinder",
            "image-setter-paper",
            "imaging-cylinder",
            "labels",
            "laminating-foil",
            "letterhead",
            "mounting-tape",
            "multi-layer",
            "multi-part-form",
            "other",
            "paper",
            "photographic",
            "photographic-film",
            "photographic-glossy",
            "photographic-high-gloss",
            "photographic-matte",
            "photographic-satin",
            "photographic-semi-gloss",
            "plate",
            "polyester",
            "pre-cut-tabs",
            "roll",
            "screen",
            "screen-paged",
            "self-adhesive",
            "shrink-foil",
            "single-face",
            "single-wall",
            "sleeve",
            "stationery",
            "stationery-coated",
            "stationery-fine",
            "stationery-heavyweight",
            "stationery-inkjet",
            "stationery-letterhead",
            "stationery-lightweight",
            "stationery-preprinted",
            "stationery-prepunched",
            "tab-stock",
            "tractor",
            "transparency",
            "triple-wall",
            "wet-film"
        ]);
        $keywords["media-type-supported"]                       = setof_keyword_name(
            $keywords["media-type"]
        );
        $keywords["multiple-document-handling"]                 = keyword([
            "separate-documents-collated-copies",
            "separate-documents-uncollated-copies",
            "single-document",
            "single-document-new-sheet"
        ]);
        $keywords["multiple-document-handling-default"]         = keyword(
            $keywords["multiple-document-handling"]
        );
        $keywords["multiple-document-handling-supported"]       = setof_keyword(
            $keywords["multiple-document-handling"]
        );
        $keywords["multiple-operation-timeout-action"]          = keyword([
            "abort-job",
            "hold-job",
            "process-job"
        ]);
        $keywords["notify-events"]                              = setof_keyword([
            "job-completed",
            "job-config-changed",
            "job-created",
            "job-progress",
            "job-state-changed",
            "job-stopped",
            "none",
            "printer-config-changed",
            "printer-finishings-changed",
            "printer-media-changed",
            "printer-queue-order-changed",
            "printer-restarted",
            "printer-shutdown",
            "printer-state-changed",
            "printer-stopped"
        ]);
        $keywords["notify-events-default"]                      = setof_keyword(
            $keywords["notify-events"]
        );
        $keywords["notify-events-supported"]                    = setof_keyword(
            $keywords["notify-events"]
        );
        $keywords["notify-pull-method"]                         = keyword([
            "ippget"
        ]);
        $keywords["notify-pull-method-supported"]               = setof_keyword(
            $keywords["notify-pull-method"]
        );
        $keywords["notify-subscribed-event"]                    = keyword(
            $keywords["notify-events"]
        );
        $keywords["output-bin"]                                 = keyword_name([
            "bottom",
            "center",
            "face-down",
            "face-up",
            "large-capacity",
            "left",
            "mailbox-1",
            "mailbox-2",
            "mailbox-3",
            "mailbox-4",
            "mailbox-5",
            "mailbox-6",
            "mailbox-7",
            "mailbox-8",
            "mailbox-9",
            "mailbox-10",
            "middle",
            "my-mailbox",
            "rear",
            "right",
            "side",
            "stacker-1",
            "stacker-2",
            "stacker-3",
            "stacker-4",
            "stacker-5",
            "stacker-6",
            "stacker-7",
            "stacker-8",
            "stacker-9",
            "stacker-10",
            "top",
            "tray-1",
            "tray-2",
            "tray-3",
            "tray-4",
            "tray-5",
            "tray-6",
            "tray-7",
            "tray-8",
            "tray-9",
            "tray-10"
        ]);
        $keywords["job-accounting-output-bin"]                  = keyword_name(
            $keywords["output-bin"]
        );
        $keywords["output-bin-default"]                         = keyword_name(
            $keywords["output-bin"]
        );
        $keywords["output-bin-supported"]                       = setof_keyword_name(
            $keywords["output-bin"]
        );
        $keywords["page-delivery"]                              = keyword([
            "reverse-order-face-down",
            "reverse-order-face-up",
            "same-order-face-down",
            "same-order-face-up",
            "system-specified"
        ]);
        $keywords["page-delivery-default"]                      = keyword(
            $keywords["page-delivery"]
        );
        $keywords["page-delivery-supported"]                    = setof_keyword(
            $keywords["page-delivery"]
        );
        $keywords["page-order-received"]                        = keyword([
            "1-to-n-order",
            "n-to-1-order"
        ]);
        $keywords["page-order-received-default"]                = keyword(
            $keywords["page-order-received"]
        );
        $keywords["page-order-received-supported"]              = setof_keyword(
            $keywords["page-order-received"]
        );
        $keywords["current-page-order"]                         = keyword(
            $keywords["page-order-received"]
        );
        $keywords["pdl-init-file-supported"]                    = setof_keyword([
            "pdl-init-file-entry",
            "pdl-init-file-location",
            "pdl-init-file-name"
        ]);
        $keywords["pdl-override-supported"]                     = keyword([
            "attempted",
            "guaranteed",
            "not-attempted"
        ]);
        $keywords["presentation-direction-number-up"]           = keyword([
            "tobottom-toleft",
            "tobottom-toright",
            "toleft-tobottom",
            "toleft-totop",
            "toright-tobottom",
            "toright-totop",
            "totop-toleft",
            "totop-toright"
        ]);
        $keywords["presentation-direction-number-up-default"]   = keyword(
            $keywords["presentation-direction-number-up"]
        );
        $keywords["presentation-direction-number-up-supported"] = setof_keyword(
            $keywords["presentation-direction-number-up"]
        );
        $keywords["print-color-mode"]                           = keyword([
            "auto",
            "bi-level",
            "color",
            "highlight",
            "monochrome",
            "process-bi-level",
            "process-monochrome"
        ]);
        $keywords["print-color-mode-default"]                   = keyword(
            $keywords["print-color-mode"]
        );
        $keywords["print-color-mode-supported"]                 = setof_keyword(
            $keywords["print-color-mode"]
        );
        $keywords["print-content-optimize"]                     = keyword([
            "auto",
            "graphic",
            "photo",
            "text",
            "text-and-graphic"
        ]);
        $keywords["print-content-optimize-default"]             = keyword(
            $keywords["print-content-optimize"]
        );
        $keywords["print-content-optimize-supported"]           = setof_keyword(
            $keywords["print-content-optimize"]
        );
        $keywords["print-rendering-intent"]                     = keyword([
            "absolute",
            "auto",
            "perceptual",
            "relative",
            "relative-bpc",
            "saturation"
        ]);
        $keywords["print-rendering-intent-default"]             = keyword(
            $keywords["print-rendering-intent"]
        );
        $keywords["print-rendering-intent-supported"]           = setof_keyword(
            $keywords["print-rendering-intent"]
        );
        $keywords["printer-get-attributes-supported"]           = setof_keyword(
//  Any Job Template attribute
//  Any job creation Operation attribute keyword name
            $Job_Template_and_Operation_attribute_names
        );
        $keywords["printer-mandatory-job-attributes"]           = setof_keyword(
//	Any Job Template attribute
//	Any Operation attribute at the job level
//this probably isn't quite right...
            $Job_Template_and_Operation_attribute_names
        );
        $keywords["printer-settable-attributes-supported"]      = setof_keyword(
//  Any read-write Printer attribute keyword name
            $Printer_attribute_names
        );
        $keywords["printer-state-reasons"]                      = setof_keyword([
            "alert-removal-of-binary-change-entry",
            "bander-added",
            "bander-almost-empty",
            "bander-almost-full",
            "bander-at-limit",
            "bander-closed",
            "bander-configuration-change",
            "bander-cover-closed",
            "bander-cover-open",
            "bander-empty",
            "bander-full",
            "bander-interlock-closed",
            "bander-interlock-open",
            "bander-jam",
            "bander-life-almost-over",
            "bander-life-over",
            "bander-memory-exhausted",
            "bander-missing",
            "bander-motor-failure",
            "bander-near-limit",
            "bander-offline",
            "bander-opened",
            "bander-over-temperature",
            "bander-power-saver",
            "bander-recoverable-failure",
            "bander-recoverable-storage-error",
            "bander-removed",
            "bander-resource-added",
            "bander-resource-removed",
            "bander-thermistor-failure",
            "bander-timing-failure",
            "bander-turned-off",
            "bander-turned-on",
            "bander-under-temperature",
            "bander-unrecoverable-failure",
            "bander-unrecoverable-storage-error",
            "bander-warming-up",
            "binder-added",
            "binder-almost-empty",
            "binder-almost-full",
            "binder-at-limit",
            "binder-closed",
            "binder-configuration-change",
            "binder-cover-closed",
            "binder-cover-open",
            "binder-empty",
            "binder-full",
            "binder-interlock-closed",
            "binder-interlock-open",
            "binder-jam",
            "binder-life-almost-over",
            "binder-life-over",
            "binder-memory-exhausted",
            "binder-missing",
            "binder-motor-failure",
            "binder-near-limit",
            "binder-offline",
            "binder-opened",
            "binder-over-temperature",
            "binder-power-saver",
            "binder-recoverable-failure",
            "binder-recoverable-storage-error",
            "binder-removed",
            "binder-resource-added",
            "binder-resource-removed",
            "binder-thermistor-failure",
            "binder-timing-failure",
            "binder-turned-off",
            "binder-turned-on",
            "binder-under-temperature",
            "binder-unrecoverable-failure",
            "binder-unrecoverable-storage-error",
            "binder-warming-up",
            "cleaner-life-almost-over",
            "cleaner-life-over",
            "configuration-change",
            "connecting-to-device",
            "cover-open",
            "deactivated",
            "developer-empty",
            "developer-low",
            "die-cutter-added",
            "die-cutter-almost-empty",
            "die-cutter-almost-full",
            "die-cutter-at-limit",
            "die-cutter-closed",
            "die-cutter-configuration-change",
            "die-cutter-cover-closed",
            "die-cutter-cover-open",
            "die-cutter-empty",
            "die-cutter-full",
            "die-cutter-interlock-closed",
            "die-cutter-interlock-open",
            "die-cutter-jam",
            "die-cutter-life-almost-over",
            "die-cutter-life-over",
            "die-cutter-memory-exhausted",
            "die-cutter-missing",
            "die-cutter-motor-failure",
            "die-cutter-near-limit",
            "die-cutter-offline",
            "die-cutter-opened",
            "die-cutter-over-temperature",
            "die-cutter-power-saver",
            "die-cutter-recoverable-failure",
            "die-cutter-recoverable-storage-error",
            "die-cutter-removed",
            "die-cutter-resource-added",
            "die-cutter-resource-removed",
            "die-cutter-thermistor-failure",
            "die-cutter-timing-failure",
            "die-cutter-turned-off",
            "die-cutter-turned-on",
            "die-cutter-under-temperature",
            "die-cutter-unrecoverable-failure",
            "die-cutter-unrecoverable-storage-error",
            "die-cutter-warming-up",
            "door-open",
            "folder-added",
            "folder-almost-empty",
            "folder-almost-full",
            "folder-at-limit",
            "folder-closed",
            "folder-configuration-change",
            "folder-cover-closed",
            "folder-cover-open",
            "folder-empty",
            "folder-full",
            "folder-interlock-closed",
            "folder-interlock-open",
            "folder-jam",
            "folder-life-almost-over",
            "folder-life-over",
            "folder-memory-exhausted",
            "folder-missing",
            "folder-motor-failure",
            "folder-near-limit",
            "folder-offline",
            "folder-opened",
            "folder-over-temperature",
            "folder-power-saver",
            "folder-recoverable-failure",
            "folder-recoverable-storage-error",
            "folder-removed",
            "folder-resource-added",
            "folder-resource-removed",
            "folder-thermistor-failure",
            "folder-timing-failure",
            "folder-turned-off",
            "folder-turned-on",
            "folder-under-temperature",
            "folder-unrecoverable-failure",
            "folder-unrecoverable-storage-error",
            "folder-warming-up",
            "fuser-over-temp",
            "fuser-under-temp",
            "imprinter-added",
            "imprinter-almost-empty",
            "imprinter-almost-full",
            "imprinter-at-limit",
            "imprinter-closed",
            "imprinter-configuration-change",
            "imprinter-cover-closed",
            "imprinter-cover-open",
            "imprinter-empty",
            "imprinter-full",
            "imprinter-interlock-closed",
            "imprinter-interlock-open",
            "imprinter-jam",
            "imprinter-life-almost-over",
            "imprinter-life-over",
            "imprinter-memory-exhausted",
            "imprinter-missing",
            "imprinter-motor-failure",
            "imprinter-near-limit",
            "imprinter-offline",
            "imprinter-opened",
            "imprinter-over-temperature",
            "imprinter-power-saver",
            "imprinter-recoverable-failure",
            "imprinter-recoverable-storage-error",
            "imprinter-removed",
            "imprinter-resource-added",
            "imprinter-resource-removed",
            "imprinter-thermistor-failure",
            "imprinter-timing-failure",
            "imprinter-turned-off",
            "imprinter-turned-on",
            "imprinter-under-temperature",
            "imprinter-unrecoverable-failure",
            "imprinter-unrecoverable-storage-error",
            "imprinter-warming-up",
            "input-cannot-feed-size-selected",
            "input-manual-input-request",
            "input-media-color-change",
            "input-media-form-parts-change",
            "input-media-size-change",
            "input-media-type-change",
            "input-media-weight-change",
            "input-tray-elevation-failure",
            "input-tray-missing",
            "input-tray-position-failure",
            "inserter-added",
            "inserter-almost-empty",
            "inserter-almost-full",
            "inserter-at-limit",
            "inserter-closed",
            "inserter-configuration-change",
            "inserter-cover-closed",
            "inserter-cover-open",
            "inserter-empty",
            "inserter-full",
            "inserter-interlock-closed",
            "inserter-interlock-open",
            "inserter-jam",
            "inserter-life-almost-over",
            "inserter-life-over",
            "inserter-memory-exhausted",
            "inserter-missing",
            "inserter-motor-failure",
            "inserter-near-limit",
            "inserter-offline",
            "inserter-opened",
            "inserter-over-temperature",
            "inserter-power-saver",
            "inserter-recoverable-failure",
            "inserter-recoverable-storage-error",
            "inserter-removed",
            "inserter-resource-added",
            "inserter-resource-removed",
            "inserter-thermistor-failure",
            "inserter-timing-failure",
            "inserter-turned-off",
            "inserter-turned-on",
            "inserter-under-temperature",
            "inserter-unrecoverable-failure",
            "inserter-unrecoverable-storage-error",
            "inserter-warming-up",
            "interlock-closed",
            "interlock-open",
            "interpreter-cartridge-added",
            "interpreter-cartridge-deleted",
            "interpreter-complex-page-encountered",
            "interpreter-memory-decrease",
            "interpreter-memory-increase",
            "interpreter-resource-added",
            "interpreter-resource-deleted",
            "interpreter-resource-unavailable",
            "make-envelope-added",
            "make-envelope-almost-empty",
            "make-envelope-almost-full",
            "make-envelope-at-limit",
            "make-envelope-closed",
            "make-envelope-configuration-change",
            "make-envelope-cover-closed",
            "make-envelope-cover-open",
            "make-envelope-empty",
            "make-envelope-full",
            "make-envelope-interlock-closed",
            "make-envelope-interlock-open",
            "make-envelope-jam",
            "make-envelope-life-almost-over",
            "make-envelope-life-over",
            "make-envelope-memory-exhausted",
            "make-envelope-missing",
            "make-envelope-motor-failure",
            "make-envelope-near-limit",
            "make-envelope-offline",
            "make-envelope-opened",
            "make-envelope-over-temperature",
            "make-envelope-power-saver",
            "make-envelope-recoverable-failure",
            "make-envelope-recoverable-storage-error",
            "make-envelope-removed",
            "make-envelope-resource-added",
            "make-envelope-resource-removed",
            "make-envelope-thermistor-failure",
            "make-envelope-timing-failure",
            "make-envelope-turned-off",
            "make-envelope-turned-on",
            "make-envelope-under-temperature",
            "make-envelope-unrecoverable-failure",
            "make-envelope-unrecoverable-storage-error",
            "make-envelope-warming-up",
            "marker-adjusting-print-quality",
            "marker-developer-almost-empty",
            "marker-developer-empty",
            "marker-fuser-thermistor-failure",
            "marker-fuser-timing-failure",
            "marker-ink-almost-empty",
            "marker-ink-empty",
            "marker-print-ribbon-almost-empty",
            "marker-print-ribbon-empty",
            "marker-supply-empty",
            "marker-supply-low",
            "marker-toner-cartridge-missing",
            "marker-waste-almost-full",
            "marker-waste-full",
            "marker-waste-ink-receptacle-almost-full",
            "marker-waste-ink-receptacle-full",
            "marker-waste-toner-receptacle-almost-full",
            "marker-waste-toner-receptacle-full",
            "media-empty",
            "media-jam",
            "media-low",
            "media-needed",
            "media-path-cannot-duplex-media-selected",
            "media-path-media-tray-almost-full",
            "media-path-media-tray-full",
            "media-path-media-tray-missing",
            "moving-to-paused",
            "none",
            "opc-life-over",
            "opc-near-eol",
            "other",
            "output-area-almost-full",
            "output-area-full",
            "output-mailbox-select-failure",
            "output-tray-missing",
            "paused",
            "perforater-added",
            "perforater-almost-empty",
            "perforater-almost-full",
            "perforater-at-limit",
            "perforater-closed",
            "perforater-configuration-change",
            "perforater-cover-closed",
            "perforater-cover-open",
            "perforater-empty",
            "perforater-full",
            "perforater-interlock-closed",
            "perforater-interlock-open",
            "perforater-jam",
            "perforater-life-almost-over",
            "perforater-life-over",
            "perforater-memory-exhausted",
            "perforater-missing",
            "perforater-motor-failure",
            "perforater-near-limit",
            "perforater-offline",
            "perforater-opened",
            "perforater-over-temperature",
            "perforater-power-saver",
            "perforater-recoverable-failure",
            "perforater-recoverable-storage-error",
            "perforater-removed",
            "perforater-resource-added",
            "perforater-resource-removed",
            "perforater-thermistor-failure",
            "perforater-timing-failure",
            "perforater-turned-off",
            "perforater-turned-on",
            "perforater-under-temperature",
            "perforater-unrecoverable-failure",
            "perforater-unrecoverable-storage-error",
            "perforater-warming-up",
            "power-down",
            "power-up",
            "printer-manual-reset",
            "printer-nms-reset",
            "printer-ready-to-print",
            "puncher-added",
            "puncher-almost-empty",
            "puncher-almost-full",
            "puncher-at-limit",
            "puncher-closed",
            "puncher-configuration-change",
            "puncher-cover-closed",
            "puncher-cover-open",
            "puncher-empty",
            "puncher-full",
            "puncher-interlock-closed",
            "puncher-interlock-open",
            "puncher-jam",
            "puncher-life-almost-over",
            "puncher-life-over",
            "puncher-memory-exhausted",
            "puncher-missing",
            "puncher-motor-failure",
            "puncher-near-limit",
            "puncher-offline",
            "puncher-opened",
            "puncher-over-temperature",
            "puncher-power-saver",
            "puncher-recoverable-failure",
            "puncher-recoverable-storage-error",
            "puncher-removed",
            "puncher-resource-added",
            "puncher-resource-removed",
            "puncher-thermistor-failure",
            "puncher-timing-failure",
            "puncher-turned-off",
            "puncher-turned-on",
            "puncher-under-temperature",
            "puncher-unrecoverable-failure",
            "puncher-unrecoverable-storage-error",
            "puncher-warming-up",
            "separation-cutter-added",
            "separation-cutter-almost-empty",
            "separation-cutter-almost-full",
            "separation-cutter-at-limit",
            "separation-cutter-closed",
            "separation-cutter-configuration-change",
            "separation-cutter-cover-closed",
            "separation-cutter-cover-open",
            "separation-cutter-empty",
            "separation-cutter-full",
            "separation-cutter-interlock-closed",
            "separation-cutter-interlock-open",
            "separation-cutter-jam",
            "separation-cutter-life-almost-over",
            "separation-cutter-life-over",
            "separation-cutter-memory-exhausted",
            "separation-cutter-missing",
            "separation-cutter-motor-failure",
            "separation-cutter-near-limit",
            "separation-cutter-offline",
            "separation-cutter-opened",
            "separation-cutter-over-temperature",
            "separation-cutter-power-saver",
            "separation-cutter-recoverable-failure",
            "separation-cutter-recoverable-storage-error",
            "separation-cutter-removed",
            "separation-cutter-resource-added",
            "separation-cutter-resource-removed",
            "separation-cutter-thermistor-failure",
            "separation-cutter-timing-failure",
            "separation-cutter-turned-off",
            "separation-cutter-turned-on",
            "separation-cutter-under-temperature",
            "separation-cutter-unrecoverable-failure",
            "separation-cutter-unrecoverable-storage-error",
            "separation-cutter-warming-up",
            "sheet-rotator-added",
            "sheet-rotator-almost-empty",
            "sheet-rotator-almost-full",
            "sheet-rotator-at-limit",
            "sheet-rotator-closed",
            "sheet-rotator-configuration-change",
            "sheet-rotator-cover-closed",
            "sheet-rotator-cover-open",
            "sheet-rotator-empty",
            "sheet-rotator-full",
            "sheet-rotator-interlock-closed",
            "sheet-rotator-interlock-open",
            "sheet-rotator-jam",
            "sheet-rotator-life-almost-over",
            "sheet-rotator-life-over",
            "sheet-rotator-memory-exhausted",
            "sheet-rotator-missing",
            "sheet-rotator-motor-failure",
            "sheet-rotator-near-limit",
            "sheet-rotator-offline",
            "sheet-rotator-opened",
            "sheet-rotator-over-temperature",
            "sheet-rotator-power-saver",
            "sheet-rotator-recoverable-failure",
            "sheet-rotator-recoverable-storage-error",
            "sheet-rotator-removed",
            "sheet-rotator-resource-added",
            "sheet-rotator-resource-removed",
            "sheet-rotator-thermistor-failure",
            "sheet-rotator-timing-failure",
            "sheet-rotator-turned-off",
            "sheet-rotator-turned-on",
            "sheet-rotator-under-temperature",
            "sheet-rotator-unrecoverable-failure",
            "sheet-rotator-unrecoverable-storage-error",
            "sheet-rotator-warming-up",
            "shutdown",
            "slitter-added",
            "slitter-almost-empty",
            "slitter-almost-full",
            "slitter-at-limit",
            "slitter-closed",
            "slitter-configuration-change",
            "slitter-cover-closed",
            "slitter-cover-open",
            "slitter-empty",
            "slitter-full",
            "slitter-interlock-closed",
            "slitter-interlock-open",
            "slitter-jam",
            "slitter-life-almost-over",
            "slitter-life-over",
            "slitter-memory-exhausted",
            "slitter-missing",
            "slitter-motor-failure",
            "slitter-near-limit",
            "slitter-offline",
            "slitter-opened",
            "slitter-over-temperature",
            "slitter-power-saver",
            "slitter-recoverable-failure",
            "slitter-recoverable-storage-error",
            "slitter-removed",
            "slitter-resource-added",
            "slitter-resource-removed",
            "slitter-thermistor-failure",
            "slitter-timing-failure",
            "slitter-turned-off",
            "slitter-turned-on",
            "slitter-under-temperature",
            "slitter-unrecoverable-failure",
            "slitter-unrecoverable-storage-error",
            "slitter-warming-up",
            "spool-area-full",
            "stacker-added",
            "stacker-almost-empty",
            "stacker-almost-full",
            "stacker-at-limit",
            "stacker-closed",
            "stacker-configuration-change",
            "stacker-cover-closed",
            "stacker-cover-open",
            "stacker-empty",
            "stacker-full",
            "stacker-interlock-closed",
            "stacker-interlock-open",
            "stacker-jam",
            "stacker-life-almost-over",
            "stacker-life-over",
            "stacker-memory-exhausted",
            "stacker-missing",
            "stacker-motor-failure",
            "stacker-near-limit",
            "stacker-offline",
            "stacker-opened",
            "stacker-over-temperature",
            "stacker-power-saver",
            "stacker-recoverable-failure",
            "stacker-recoverable-storage-error",
            "stacker-removed",
            "stacker-resource-added",
            "stacker-resource-removed",
            "stacker-thermistor-failure",
            "stacker-timing-failure",
            "stacker-turned-off",
            "stacker-turned-on",
            "stacker-under-temperature",
            "stacker-unrecoverable-failure",
            "stacker-unrecoverable-storage-error",
            "stacker-warming-up",
            "stapler-added",
            "stapler-almost-empty",
            "stapler-almost-full",
            "stapler-at-limit",
            "stapler-closed",
            "stapler-configuration-change",
            "stapler-cover-closed",
            "stapler-cover-open",
            "stapler-empty",
            "stapler-full",
            "stapler-interlock-closed",
            "stapler-interlock-open",
            "stapler-jam",
            "stapler-life-almost-over",
            "stapler-life-over",
            "stapler-memory-exhausted",
            "stapler-missing",
            "stapler-motor-failure",
            "stapler-near-limit",
            "stapler-offline",
            "stapler-opened",
            "stapler-over-temperature",
            "stapler-power-saver",
            "stapler-recoverable-failure",
            "stapler-recoverable-storage-error",
            "stapler-removed",
            "stapler-resource-added",
            "stapler-resource-removed",
            "stapler-thermistor-failure",
            "stapler-timing-failure",
            "stapler-turned-off",
            "stapler-turned-on",
            "stapler-under-temperature",
            "stapler-unrecoverable-failure",
            "stapler-unrecoverable-storage-error",
            "stapler-warming-up",
            "stitcher-added",
            "stitcher-almost-empty",
            "stitcher-almost-full",
            "stitcher-at-limit",
            "stitcher-closed",
            "stitcher-configuration-change",
            "stitcher-cover-closed",
            "stitcher-cover-open",
            "stitcher-empty",
            "stitcher-full",
            "stitcher-interlock-closed",
            "stitcher-interlock-open",
            "stitcher-jam",
            "stitcher-life-almost-over",
            "stitcher-life-over",
            "stitcher-memory-exhausted",
            "stitcher-missing",
            "stitcher-motor-failure",
            "stitcher-near-limit",
            "stitcher-offline",
            "stitcher-opened",
            "stitcher-over-temperature",
            "stitcher-power-saver",
            "stitcher-recoverable-failure",
            "stitcher-recoverable-storage-error",
            "stitcher-removed",
            "stitcher-resource-added",
            "stitcher-resource-removed",
            "stitcher-thermistor-failure",
            "stitcher-timing-failure",
            "stitcher-turned-off",
            "stitcher-turned-on",
            "stitcher-under-temperature",
            "stitcher-unrecoverable-failure",
            "stitcher-unrecoverable-storage-error",
            "stitcher-warming-up",
            "stopped-partly",
            "stopping",
            "subunit-added",
            "subunit-almost-empty",
            "subunit-almost-full",
            "subunit-at-limit",
            "subunit-closed",
            "subunit-empty",
            "subunit-full",
            "subunit-life-almost-over",
            "subunit-life-over",
            "subunit-memory-exhausted",
            "subunit-missing",
            "subunit-motor-failure",
            "subunit-near-limit",
            "subunit-offline",
            "subunit-opened",
            "subunit-over-temperature",
            "subunit-power-saver",
            "subunit-recoverable-failure",
            "subunit-recoverable-storage-error",
            "subunit-removed",
            "subunit-resource-added",
            "subunit-resource-removed",
            "subunit-thermistor-failure",
            "subunit-timing-Failure",
            "subunit-turned-off",
            "subunit-turned-on",
            "subunit-under-temperature",
            "subunit-unrecoverable-failure",
            "subunit-unrecoverable-storage-error",
            "subunit-warming-up",
            "timed-out",
            "toner-empty",
            "toner-low",
            "trimmer-added",
            "trimmer-added",
            "trimmer-almost-empty",
            "trimmer-almost-empty",
            "trimmer-almost-full",
            "trimmer-almost-full",
            "trimmer-at-limit",
            "trimmer-at-limit",
            "trimmer-closed",
            "trimmer-closed",
            "trimmer-configuration-change",
            "trimmer-configuration-change",
            "trimmer-cover-closed",
            "trimmer-cover-closed",
            "trimmer-cover-open",
            "trimmer-cover-open",
            "trimmer-empty",
            "trimmer-empty",
            "trimmer-full",
            "trimmer-full",
            "trimmer-interlock-closed",
            "trimmer-interlock-closed",
            "trimmer-interlock-open",
            "trimmer-interlock-open",
            "trimmer-jam",
            "trimmer-jam",
            "trimmer-life-almost-over",
            "trimmer-life-almost-over",
            "trimmer-life-over",
            "trimmer-life-over",
            "trimmer-memory-exhausted",
            "trimmer-memory-exhausted",
            "trimmer-missing",
            "trimmer-missing",
            "trimmer-motor-failure",
            "trimmer-motor-failure",
            "trimmer-near-limit",
            "trimmer-near-limit",
            "trimmer-offline",
            "trimmer-offline",
            "trimmer-opened",
            "trimmer-opened",
            "trimmer-over-temperature",
            "trimmer-over-temperature",
            "trimmer-power-saver",
            "trimmer-power-saver",
            "trimmer-recoverable-failure",
            "trimmer-recoverable-failure",
            "trimmer-recoverable-storage-error",
            "trimmer-removed",
            "trimmer-resource-added",
            "trimmer-resource-removed",
            "trimmer-thermistor-failure",
            "trimmer-timing-failure",
            "trimmer-turned-off",
            "trimmer-turned-on",
            "trimmer-under-temperature",
            "trimmer-unrecoverable-failure",
            "trimmer-unrecoverable-storage-error",
            "trimmer-warming-up",
            "unknown",
            "wrapper-added",
            "wrapper-almost-empty",
            "wrapper-almost-full",
            "wrapper-at-limit",
            "wrapper-closed",
            "wrapper-configuration-change",
            "wrapper-cover-closed",
            "wrapper-cover-open",
            "wrapper-empty",
            "wrapper-full",
            "wrapper-interlock-closed",
            "wrapper-interlock-open",
            "wrapper-jam",
            "wrapper-life-almost-over",
            "wrapper-life-over",
            "wrapper-memory-exhausted",
            "wrapper-missing",
            "wrapper-motor-failure",
            "wrapper-near-limit",
            "wrapper-offline",
            "wrapper-opened",
            "wrapper-over-temperature",
            "wrapper-power-saver",
            "wrapper-recoverable-failure",
            "wrapper-recoverable-storage-error",
            "wrapper-removed",
            "wrapper-resource-added",
            "wrapper-resource-removed",
            "wrapper-thermistor-failure",
            "wrapper-timing-failure",
            "wrapper-turned-off",
            "wrapper-turned-on",
            "wrapper-under-temperature",
            "wrapper-unrecoverable-failure",
            "wrapper-unrecoverable-storage-error",
            "wrapper-warming-up"
        ]);
        $keywords["proof-print-supported"]                      = setof_keyword([
            "media",
            "media-col",
            "proof-print-copies"
        ]);
        $keywords["pwg-raster-document-sheet-back"]             = keyword([
            "flipped",
            "manual-tumble",
            "normal",
            "rotated"
        ]);
        $keywords["pwg-raster-document-type-supported"]         = setof_keyword([
            "adobe-rgb_8",
            "adobe-rgb_16",
            "black_1",
            "black_8",
            "black_16",
            "cmyk_8",
            "cmyk_16",
            "device1_8",
            "device1_16",
            "device2_8",
            "device2_16",
            "device3_8",
            "device3_16",
            "device4_8",
            "device4_16",
            "device5_8",
            "device5_16",
            "device6_8",
            "device6_16",
            "device7_8",
            "device7_16",
            "device8_8",
            "device8_16",
            "device9_8",
            "device9_16",
            "device10_8",
            "device10_16",
            "device11_8",
            "device11_16",
            "device12_8",
            "device12_16",
            "device13_8",
            "device13_16",
            "device14_8",
            "device14_16",
            "device15_8",
            "device15_16",
            "rgb_8",
            "rgb_16",
            "sgray_1",
            "sgray_8",
            "sgray_16",
            "srgb_8",
            "srgb_16"
        ]);
        $keywords["requested-attributes"]                       = keyword([
            "all",
            "document-description",
            "document-template",
            "job-description",
            "job-template",
            "printer-description",
            "subscription-description",
            "subscription-template"
        ]);
        $keywords["save-disposition"]                           = keyword([
            "none",
            "print-save",
            "save-only"
        ]);
        $keywords["save-disposition-supported"]                 = setof_keyword(
            $keywords["save-disposition"]
        );
        $keywords["save-info-supported"]                        = setof_keyword([
            "save-document-format",
            "save-location",
            "save-name"
        ]);
        $keywords["separator-sheets-type"]                      = keyword_name([
            "both-sheets",
            "end-sheet",
            "none",
            "slip-sheets",
            "start-sheet"
        ]);
        $keywords["separator-sheets-type-supported"]            = setof_keyword_name(
            $keywords["separator-sheets-type"]
        );
        $keywords["sheet-collate"]                              = keyword([
            "collated",
            "uncollated"
        ]);
        $keywords["sheet-collate-default"]                      = keyword(
            $keywords["sheet-collate"]
        );
        $keywords["sheet-collate-supported"]                    = setof_keyword(
            $keywords["sheet-collate"]
        );
        $keywords["sides"]                                      = keyword([
            "one-sided",
            "two-sided-long-edge",
            "two-sided-short-edge"
        ]);
        $keywords["sides-default"]                              = keyword(
            $keywords["sides"]
        );
        $keywords["sides-supported"]                            = setof_keyword(
            $keywords["sides"]
        );
        $keywords["stitching-reference-edge"]                   = keyword([
            "bottom",
            "left",
            "right",
            "top"
        ]);
        $keywords["stitching-reference-edge-supported"]         = setof_keyword(
            $keywords["stitching-reference-edge"]
        );
        $keywords["stitching-supported"]                        = setof_keyword([
            "stitching-locations",
            "stitching-offset",
            "stitching-reference-edge"
        ]);
        $keywords["uri-authentication-supported"]               = setof_keyword([
            "basic",
            "certificate",
            "digest",
            "negotiate",
            "none",
            "requesting-user-name"
        ]);
        $keywords["uri-security-supported"]                     = setof_keyword([
            "none",
            "ssl3",
            "tls"
        ]);
        $keywords["which-jobs"]                                 = keyword([
            "aborted",
            "all",
            "canceled",
            "completed",
            "not-completed",
            "pending",
            "pending-held",
            "processing",
            "processing-stopped",
            "proof-print",
            "saved"
        ]);
        $keywords["which-jobs-supported"]                       = setof_keyword(
            $keywords["which-jobs"]
        );
        $keywords["x-image-position"]                           = keyword([
            "center",
            "left",
            "none",
            "right"
        ]);
        $keywords["x-image-position-default"]                   = keyword(
            $keywords["x-image-position"]
        );
        $keywords["x-image-position-supported"]                 = setof_keyword(
            $keywords["x-image-position"]
        );
        $keywords["xri-authentication-supported"]               = setof_keyword([
            "basic",
            "certificate",
            "digest",
            "none",
            "requesting-user-name"
        ]);
        $keywords["xri-security-supported"]                     = setof_keyword([
            "none",
            "ssl3",
            "tls"
        ]);
        $keywords["y-image-position"]                           = keyword([
            "bottom",
            "center",
            "none",
            "top"
        ]);
        $keywords["y-image-position-default"]                   = keyword(
            $keywords["y-image-position"]
        );
        $keywords["y-image-position-supported"]                 = setof_keyword(
            $keywords["y-image-position"]
        );

        if (!isset(self::$data)) {
            self::$data = [];
        }

        self::$data["keywords"] = $keywords;

    }

    protected static function seedStatus() {

        // Status Codes
        // ------------

        $status = [];
        /* Success 0x0000 - 0x00FF */
        $status[0x0000] = 'successful-ok';                                      //http://tools.ietf.org/html/rfc2911#section-13.1.2.1
        $status[0x0001] = 'successful-ok-ignored-or-substituted-attributes';    //http://tools.ietf.org/html/rfc2911#section-13.1.2.2 & http://tools.ietf.org/html/rfc3995#section-13.5
        $status[0x0002] = 'successful-ok-conflicting-attributes';               //http://tools.ietf.org/html/rfc2911#section-13.1.2.3
        $status[0x0003] = 'successful-ok-ignored-subscriptions';                //http://tools.ietf.org/html/rfc3995#section-12.1
        $status[0x0004] = 'successful-ok-ignored-notifications';                //http://tools.ietf.org/html/draft-ietf-ipp-indp-method-05#section-9.1.1    did not get standardized
        $status[0x0005] = 'successful-ok-too-many-events';                      //http://tools.ietf.org/html/rfc3995#section-13.4
        $status[0x0006] = 'successful-ok-but-cancel-subscription';              //http://tools.ietf.org/html/draft-ietf-ipp-indp-method-05#section-9.2.2    did not get standardized
        $status[0x0007] = 'successful-ok-events-complete';                      //http://tools.ietf.org/html/rfc3996#section-10.1

        $status[0x0400] = 'client-error-bad-request';                           //http://tools.ietf.org/html/rfc2911#section-13.1.4.1
        $status[0x0401] = 'client-error-forbidden';                             //http://tools.ietf.org/html/rfc2911#section-13.1.4.2
        $status[0x0402] = 'client-error-not-authenticated';                     //http://tools.ietf.org/html/rfc2911#section-13.1.4.3
        $status[0x0403] = 'client-error-not-authorized';                        //http://tools.ietf.org/html/rfc2911#section-13.1.4.4
        $status[0x0404] = 'client-error-not-possible';                          //http://tools.ietf.org/html/rfc2911#section-13.1.4.5
        $status[0x0405] = 'client-error-timeout';                               //http://tools.ietf.org/html/rfc2911#section-13.1.4.6
        $status[0x0406] = 'client-error-not-found';                             //http://tools.ietf.org/html/rfc2911#section-13.1.4.7
        $status[0x0407] = 'client-error-gone';                                  //http://tools.ietf.org/html/rfc2911#section-13.1.4.8
        $status[0x0408] = 'client-error-request-entity-too-large';              //http://tools.ietf.org/html/rfc2911#section-13.1.4.9
        $status[0x0409] = 'client-error-request-value-too-long';                //http://tools.ietf.org/html/rfc2911#section-13.1.4.1
        $status[0x040A] = 'client-error-document-format-not-supported';         //http://tools.ietf.org/html/rfc2911#section-13.1.4.11
        $status[0x040B] = 'client-error-attributes-or-values-not-supported';    //http://tools.ietf.org/html/rfc2911#section-13.1.4.12 & http://tools.ietf.org/html/rfc3995#section-13.2
        $status[0x040C] = 'client-error-uri-scheme-not-supported';              //http://tools.ietf.org/html/rfc2911#section-13.1.4.13 & http://tools.ietf.org/html/rfc3995#section-13.1
        $status[0x040D] = 'client-error-charset-not-supported';                 //http://tools.ietf.org/html/rfc2911#section-13.1.4.14
        $status[0x040E] = 'client-error-conflicting-attributes';                //http://tools.ietf.org/html/rfc2911#section-13.1.4.15
        $status[0x040F] = 'client-error-compression-not-supported';             //http://tools.ietf.org/html/rfc2911#section-13.1.4.16
        $status[0x0410] = 'client-error-compression-error';                     //http://tools.ietf.org/html/rfc2911#section-13.1.4.17
        $status[0x0411] = 'client-error-document-format-error';                 //http://tools.ietf.org/html/rfc2911#section-13.1.4.18
        $status[0x0412] = 'client-error-document-access-error';                 //http://tools.ietf.org/html/rfc2911#section-13.1.4.19
        $status[0x0413] = 'client-error-attributes-not-settable';               //http://tools.ietf.org/html/rfc3380#section-7.1
        $status[0x0414] = 'client-error-ignored-all-subscriptions';             //http://tools.ietf.org/html/rfc3995#section-12.2
        $status[0x0415] = 'client-error-too-many-subscriptions';                //http://tools.ietf.org/html/rfc3995#section-13.2
        $status[0x0416] = 'client-error-ignored-all-notifications';             //http://tools.ietf.org/html/draft-ietf-ipp-indp-method-06#section-9.1.2    did not get standardized
        $status[0x0417] = 'client-error-client-print-support-file-not-found';   //http://tools.ietf.org/html/draft-ietf-ipp-install-04#section-10.1         did not get standardized
        $status[0x0418] = 'client-error-document-password-error';               //ftp://ftp.pwg.org/pub/pwg/ipp/wd/wd-ippjobprinterext3v10-20120420.pdf     did not get standardized
        $status[0x0419] = 'client-error-document-permission-error';             //ftp://ftp.pwg.org/pub/pwg/ipp/wd/wd-ippjobprinterext3v10-20120420.pdf     did not get standardized
        $status[0x041A] = 'client-error-document-security-error';               //ftp://ftp.pwg.org/pub/pwg/ipp/wd/wd-ippjobprinterext3v10-20120420.pdf     did not get standardized
        $status[0x041B] = 'client-error-document-unprintable-error';            //ftp://ftp.pwg.org/pub/pwg/ipp/wd/wd-ippjobprinterext3v10-20120420.pdf     did not get standardized
        /* Server error 0x0500 - 0x05FF */
        $status[0x0500] = 'server-error-internal-error';                        //http://tools.ietf.org/html/rfc2911#section-13.1.5.1
        $status[0x0501] = 'server-error-operation-not-supported';               //http://tools.ietf.org/html/rfc2911#section-13.1.5.2
        $status[0x0502] = 'server-error-service-unavailable';                   //http://tools.ietf.org/html/rfc2911#section-13.1.5.3
        $status[0x0503] = 'server-error-version-not-supported';                 //http://tools.ietf.org/html/rfc2911#section-13.1.5.4
        $status[0x0504] = 'server-error-device-error';                          //http://tools.ietf.org/html/rfc2911#section-13.1.5.5
        $status[0x0505] = 'server-error-temporary-error';                       //http://tools.ietf.org/html/rfc2911#section-13.1.5.6
        $status[0x0506] = 'server-error-not-accepting-jobs';                    //http://tools.ietf.org/html/rfc2911#section-13.1.5.7
        $status[0x0507] = 'server-error-busy';                                  //http://tools.ietf.org/html/rfc2911#section-13.1.5.8
        $status[0x0508] = 'server-error-job-canceled';                          //http://tools.ietf.org/html/rfc2911#section-13.1.5.9
        $status[0x0509] = 'server-error-multiple-document-jobs-not-supported';  //http://tools.ietf.org/html/rfc2911#section-13.1.5.10
        $status[0x050A] = 'server-error-printer-is-deactivated';                //http://tools.ietf.org/html/rfc3998#section-5.1
        $status[0x050B] = 'server-error-too-many-jobs';                         //ftp://ftp.pwg.org/pub/pwg/candidates/cs-ippjobext10-20031031-5100.7.pdf
        $status[0x050C] = 'server-error-too-many-documents';                    //ftp://ftp.pwg.org/pub/pwg/candidates/cs-ippjobext10-20031031-5100.7.pdf

        $status = Util::xref($status);

        if (!isset(self::$data)) {
            self::$data = [];
        }

        self::$data["status"] = $status;

    }

    protected static function seed($collection = null) {

        if (isset(self::$data) && isset(self::$data[$collection])) {
            return;
        }

        switch ($collection) {
            case "enums":
                return self::seedEnums();
            case "tags":
                return self::seedTags();
            case "versions":
                return self::seedVersions();
            case "attributes":
                return self::seedAttributes();
            case "keywords":
                return self::seedKeywords();
            case "status":
                return self::seedStatus();
            default:
                self::seedEnums();
                self::seedTags();
                self::seedVersions();
                self::seedAttributes();
                self::seedKeywords();
                self::seedStatus();

        }

    }

    public static function get($collection, $key = null) {

        self::seed($collection);

        if (is_string($key)) {
            return self::$data[$collection][$key];
        }

        return self::$data[$collection];
    }

    public static function text($max = null) {

        return ["type" => "text", "max" => empty($max) ? 1023 : $max];
    }

    public static function integer($min = null, $max = null) {

        $tags = self::get("tags");

        if ($max == self::$MAX || $max === null) {
            $max = 2147483647;
        }

        if ($min === null) {
            $min = -2147483648;
        }

        return ["type" => "integer", "tag" => $tags["integer"], "min" => $min, "max" => $max];
    }

    public static function rangeOfInteger($min = null, $max = null) {

        $tags = self::get("tags");

        if ($max == self::$MAX || $max === null) {
            $max = 2147483647;
        }

        if ($min === null) {
            $min = -2147483648;
        }

        return ["type" => "rangeOfInteger", "tag" => $tags["rangeOfInteger"], "min" => $min, "max" => $max];
    }

    public static function boolean() {

        $tags = self::get("tags");

        return ["type" => "boolean", "tag" => $tags["boolean"]];
    }

    public static function charset() {

        $tags = self::get("tags");

        return ["type" => "charset", "tag" => $tags["charset"], "max" => 63];
    }

    public static function keyword() {

        $tags = self::get("tags");

        return ["type" => "keyword", "tag" => $tags["keyword"], "min" => 1, "max" => 1023];
    }

    public static function naturalLanguage() {

        $tags = self::get("tags");

        return ["type" => "naturalLanguage", "tag" => $tags["naturalLanguage"], "max" => 63];
    }

    public static function dateTime() {

        $tags = self::get("tags");

        return ["type" => "dateTime", "tag" => $tags["dateTime"]];
    }

    public static function mimeMediaType() {

        $tags = self::get("tags");

        return ["type" => "mimeMediaType", "tag" => $tags["mimeMediaType"], "max" => 255];
    }

    public static function uri($max = null) {

        $tags = self::get("tags");

        return ["type" => "uri", "tag" => $tags["uri"], "max" => empty($max) ? 1023 : $max];
    }

    public static function uriScheme() {

        $tags = self::get("tags");

        return ["type" => "uriScheme", "tag" => $tags["uriScheme"], "max" => 63];
    }

    public static function enumeration() {

        $tags = self::get("tags");

        return ["type" => "enumeration", "tag" => $tags["enum"]];
    }

    public static function resolution() {

        $tags = self::get("tags");

        return ["type" => "resolution", "tag" => $tags["resolution"]];
    }

    public static function unknown() {

        $tags = self::get("tags");

        return ["type" => "unknown", "tag" => $tags["unknown"]];
    }

    public static function name($max = null) {

        return ["type" => "name", "max" => empty($max) ? 1023 : $max];
    }

    public static function novalue() {

        $tags = self::get("tags");

        return ["type" => "novalue", "tag" => $tags["no-value"]];
    }

    public static function octetString($max = null) {

        $tags = self::get("tags");

        return ["type" => "octetString", "tag" => $tags["octetString"], "max" => empty($max) ? 1023 : $max];
    }

    // Some attributes allow alternate value syntaxes.
    // I want to keep the look and feel of the code close to
    // that of the RFCs. So, this _ (underscore) function is
    // used to group alternates and not be intrusive visually.
    protected static function underscore(...$arguments) {

        $arguments["lookup"] = [];

        $deferred = createDeferred(function () use ($arguments) {

            foreach ($arguments as $i => $a) {

                if ($i != "lookup") {
                    if (is_callable($a)) {
                        $arguments[$i] = call_user_func($a);
                    }

                    $arguments["lookup"][$arguments[$i]["type"]] = $arguments[$i];
                }
            }

            asort($arguments["lookup"]);
            $arguments["alts"] = implode(",", array_keys($arguments["lookup"]));

            return $arguments;
        });

        return Util::array_some($arguments, function ($a) {

            return isDeferred($a);
        }) ? $deferred : ($deferred->function)();

    }

    // In IPP, "1setOf" just means "Array"... but it must 1 or more items
    // In javascript, functions can't start with a number- so let's just use...
    protected static function setof($type) {

        if (isDeferred($type)) {
            return createDeferred(function () use ($type) {

                $t          = ($type->function)();
                $t["setof"] = true;

                return $t;
            });
        }
        if (is_callable($type)) {
            $type = $type();
        }
        $type["setof"] = true;

        return $type;
    }

    // In IPP, a "collection" is an set of name-value
    // pairs. In javascript, we call them "Objects".
    protected static function collection($group = null, $name = null) {

        $tags = self::get("tags");

        if (!isset($group) && !isset($name)) {
            return ["type" => "collection", "tag" => $tags["begCollection"]];
        }

        if (is_string($group)) {
            return createDeferred(function () use ($group, $name) {

                $tags       = self::get("tags");
                $attributes = self::get("attributes");

                return [
                    "type"    => "collection",
                    "tag"     => $tags["begCollection"],
                    "members" => $attributes[$group][$name]["members"]
                ];
            });
        }

        $defer = false;
        foreach ($group as $k => $v) {
            $defer = isDeferred($v);
            if ($defer) {
                break;
            }
        }

        $deferred = createDeferred(function () use ($tags, $group) {

            return [
                "type"    => "collection",
                "tag"     => $tags["begCollection"],
                "members" => self::resolve($group)
            ];
        });

        return $defer ? $deferred : ($deferred->function)();
    }

    protected static function resolve($obj) {

        if (isset($obj["type"])) {
            return $obj;
        }

        foreach (array_keys($obj) as $name) {

            $item = $obj[$name];
            if (is_callable($item)) {
                $obj[$name] = $item();
            } elseif (is_array($item) && !isset($item["type"])) {
                $obj[$name] = self::resolve($item);
            }

        }

        return $obj;
    }

}