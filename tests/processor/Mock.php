<?php

namespace voilab\cleaner\tests\processor;

use voilab\cleaner\HtmlCleaner,
    voilab\cleaner\processor\Processor;

class Mock implements Processor {

    /**
     * {@inheritdocs}
     */
    public function pre(HtmlCleaner $cleaner, $html) : string
    {
        return $html;
    }

    /**
     * {@inheritdocs}
     */
    public function post(HtmlCleaner $cleaner, $result) : string
    {
        return $result;
    }
}
