<?php

namespace Ibehbudov\PaymentGateways\Library;

use Spatie\ArrayToXml\ArrayToXml;

class XmlConverter {

    public static function xmlToArray(string $xml): array
    {
        return json_decode(
            json_encode(
                simplexml_load_string($xml)
            ),
        true);
    }

    public static function arrayToXml(array $array, string $rootElement, string $xmlEncoding): string
    {
        return ArrayToXml::convert($array, $rootElement, $xmlEncoding);
    }



}

