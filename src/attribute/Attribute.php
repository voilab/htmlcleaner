<?php

namespace voilab\cleaner\attribute;

use SimpleXMLElement,
    DOMElement;

abstract class Attribute {

    /**
     * Attribute's name
     * @var string
     */
    private $name;

    /**
     * Eventually the tag associated to the attribute
     * @var string
     */
    private $tag;

    /**
     * Attribute data
     * @var SimpleXMLElement
     */
    private $attribute;

    /**
     * Constructor
     *
     * @param string $name attribute's name
     * @param string $tag an optional tag name linked to the attribute
     */
    public function __construct($name, $tag = null)
    {
        $this->name = $name;
        $this->tag = $tag;
    }

    /**
     * Get the attribute's name
     *
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Get the tag name associated to this attribute. Can be null
     *
     * @return string|null
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Get the attribute's data
     *
     * @return SimpleXMLElement
     */
    public function getAttribute() : SimpleXMLElement
    {
        return $this->attribute;
    }

    /**
     * Set attribute's data
     *
     * @param SimpleXMLElement $attr
     * @return static
     */
    public function setAttribute(SimpleXMLElement $attr) : self
    {
        $this->attribute = $attr;
        return $this;
    }

    /**
     * Determine if the attribute shall pass (and appear in the final result)
     * or need to be changed or deleted
     *
     * @see clean()
     * @return bool false to clean it (see {@link clean()})
     */
    abstract public function pass() : bool;

    /**
     * Clean the attribute, either by removing it, or by changing its content
     *
     * @param DOMElement $element
     * @return void
     */
    abstract public function clean(DOMElement $element);
}
