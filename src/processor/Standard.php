<?php

namespace voilab\cleaner\processor;

use voilab\cleaner\HtmlCleaner;

class Standard implements Processor {

    /**
     * {@inheritdocs}
     */
    public function pre(HtmlCleaner $cleaner, $html) : string
    {
        $tags = '<' . implode('><', $cleaner->getAllowedTags()) . '>';
        // simply strip tags except allowed ones.
        return strip_tags((string) $html, $tags);
    }

    /**
     * {@inheritdocs}
     */
    public function post(HtmlCleaner $cleaner, $result) : string
    {
        // removes carriage returns, so html is compacted
        return str_replace(["\r\n", "\n", "\r"], '', $result);
    }
}
