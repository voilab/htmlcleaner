<?php

namespace voilab\cleaner\attribute;

use DOMElement;

class Js extends Attribute {

    /**
     * {@inheritdocs}
     */
    public function pass() : bool
    {
        // refuse attribute content which contains javascript operations
        return strpos((string) $this->getAttribute(), 'javascript:') === false;
    }

    /**
     * {@inheritdocs}
     */
    public function clean(DOMElement $element)
    {
        // first remove the bad href attribute
        $element->removeAttribute($this->getName());
        // then add a href attribute without risks
        $element->setAttribute($this->getName(), '#');
    }
}
