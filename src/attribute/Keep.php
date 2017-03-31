<?php

namespace voilab\cleaner\attribute;

use DOMElement;

class Keep extends Attribute {

    /**
     * {@inheritdocs}
     */
    public function pass() : bool
    {
        // always accept attribute to keep (obviously...)
        return true;
    }

    /**
     * {@inheritdocs}
     */
    public function clean(DOMElement $element)
    {}
}
