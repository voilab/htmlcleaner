<?php

namespace voilab\cleaner\processor;

use voilab\cleaner\HtmlCleaner;

interface Processor {

    /**
     * Prepare HTML before loading it into a simplexml element
     *
     * @param HtmlCleaner $cleaner
     * @param mixed $rawHtml can be html string or any html representation
     * @return string
     */
    public function pre(HtmlCleaner $cleaner, $rawHtml) : string;

    /**
     * Change parts of the cleaned html, after everything is cleaned
     *
     * @param HtmlCleaner $cleaner
     * @param string $cleanedHtml
     * @return string
     */
    public function post(HtmlCleaner $cleaner, $cleanedHtml) : string;
}
