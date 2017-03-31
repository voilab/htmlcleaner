<?php

namespace voilab\cleaner\attribute;

use DOMElement;

class Remove extends Attribute {

    /**
     * {@inheritdocs}
     */
    public function pass() : bool
    {
        // always refuse attribute to be removed. Most of the time, this
        // behaviour is internal to the cleaner process. User should not have
        // to instanciate this class.
        return false;
    }

    /**
     * {@inheritdocs}
     */
    public function clean(DOMElement $element)
    {
        $element->removeAttribute($this->getName());
    }
}
