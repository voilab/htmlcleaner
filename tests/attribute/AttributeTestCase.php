<?php

namespace voilab\cleaner\tests\attribute;

use PHPUnit\Framework\TestCase,
    SimpleXMLElement,
    Exception;

class AttributeTestCase extends TestCase {

    protected function getAttribute($sxml, $name) {
        foreach ($sxml->attributes() as $key => $attr) {
            if ($key === $name) {
                return clone $attr;
            }
        }
        throw new Exception(sprintf("Attribute %s not found", $name));
    }

    protected function getElements($html) {
        $xml = $this->getSimpleXmlElement($html);
        $dom = $this->getDomElement($xml);
        return [$xml, $dom];
    }

    private function getSimpleXmlElement($html) {
        $xml = new SimpleXMLElement('<root>' . $html . '</root>');
        foreach ($xml->children() as $child) {
            return $child;
        }
        throw new Exception(sprintf("No child in HTML [%s]", $html));
    }

    private function getDomElement(SimpleXMLElement $el) {
        return dom_import_simplexml($el);
    }
}
