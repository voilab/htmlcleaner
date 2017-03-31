<?php

namespace voilab\cleaner;

use SimpleXMLElement,
    voilab\cleaner\attribute\Attribute,
    voilab\cleaner\attribute\Keep,
    voilab\cleaner\attribute\Remove,
    voilab\cleaner\processor\Processor,
    voilab\cleaner\processor\Standard as StandardProcessor,
    Exception;

class HtmlCleaner {

    /**
     * An array of allowed tags
     * @var array
     */
    private $allowedTags = [];

    /**
     * An array of attribute managers
     * @var Attribute[]
     */
    private $allowedAttributes = [];

    /**
     * A processor to prepare and finish the html string
     * @var Processor
     */
    private $processor;

    /**
     * HTML cleaner constructor
     *
     * @param Processor $processor Defaults to standard processor
     */
    public function __construct(Processor $processor = null)
    {
        if (!$processor) {
            $processor = new StandardProcessor();
        }
        $this->setProcessor($processor);
    }

    /**
     * Set a processor. It is used to prepare HTML before creating the
     * SimpleXMLElement, and after the cleaner process (for example to remove
     * carriage returns)
     *
     * @param Processor $processor
     * @return static
     */
    public function setProcessor(Processor $processor) : self
    {
        $this->processor = $processor;
        return $this;
    }

    /**
     * Returns the configured processor.
     *
     * @see setProcessor()
     * @return Processor
     */
    public function getProcessor() : Processor
    {
        return $this->processor;
    }

    /**
     * Set allowed tags, without <> (['p', 'ul', 'li'])
     *
     * @param array $tags
     * @return static
     */
    public function setAllowedTags(array $tags) : self
    {
        $this->allowedTags = [];
        return $this->addAllowedTags($tags);
    }

    /**
     * Set allowed tags
     *
     * @see setAllowedTags()
     * @param array $tags
     * @return static
     */
    public function addAllowedTags(array $tags) : self
    {
        foreach ($tags as $tag) {
            $this->allowedTags[] = str_replace(['<', '>'], '', $tag);
        }
        $this->allowedTags = array_unique($this->allowedTags);
        return $this;
    }

    /**
     * Return allowed tags
     *
     * @return array
     */
    public function getAllowedTags() : array
    {
        return $this->allowedTags;
    }

    /**
     * Set allowed attributes. If an attribute is provided as a string, it will
     * be converted into a Keep attribute class
     *
     * @see setAllowedAttribute()
     * @param Attribute[] $attributes
     * @return static
     */
    public function addAllowedAttributes(array $attributes) : self
    {
        foreach ($attributes as $attr) {
            if (is_string($attr)) {
                $attr = new Keep($attr);
            }
            $this->setAllowedAttribute($attr);
        }
        return $this;
    }

    /**
     * Set an attribute. If it is already defined, it will be replaced by the
     * new one.
     *
     * @param Attribute $attr
     * @return static
     */
    public function setAllowedAttribute(Attribute $attr) : self
    {
        $this->allowedAttributes[$attr->getName()] = $attr;
        return $this;
    }

    /**
     * Remove an attribute from the whitelist
     *
     * @param string $name attribute's name
     * @return bool true if it has been removed, false if it doesn't exist
     */
    public function removeAllowedAttribute($name) : bool
    {
        if ($this->hasAllowedAttribute($name)) {
            unset($this->allowedAttributes[$name]);
            return true;
        }
        return false;
    }

    /**
     * Return allowed attributes
     *
     * @return array
     */
    public function getAllowedAttributes() : array
    {
        return $this->allowedAttributes;
    }

    /**
     * Get an allowed attribute
     *
     * @param string $name attribute's name
     * @return Attribute
     * @throws Exception if attribute doesn't exist
     */
    public function getAllowedAttribute($name) : Attribute
    {
        if (!$this->hasAllowedAttribute($name)) {
            throw new Exception(sprintf('Attribute %s does not exist', $name));
        }
        return $this->allowedAttributes[$name];
    }

    /**
     * Check if an allowed attribute exists
     *
     * @param string $name attribute's name
     * @return bool
     */
    public function hasAllowedAttribute($name) : bool
    {
        return isset($this->allowedAttributes[$name]);
    }

    /**
     * Process html string and clean everything possible
     *
     * @param mixed $html the base html string provided by the user
     * @return string the cleaned html string
     */
    public function clean($html) : string
    {
        $processor = $this->getProcessor();
        $pre_html = $processor->pre($this, $html);

        // create a fake root element, so we can process strings with many
        // roots. Eg: <p>1</p><p>2</p> becomes <root><p>1</p><p>2</p></root>
        $xml = @simplexml_load_string('<root>' . $pre_html . '</root>');
        if ($xml === false) {
            throw new Exception("Bad formatted HTML");
        }
        $cleaned_html = '';
        if (count($xml->children())) {
            foreach ($xml->children() as $root) {
                $this->parse($root);
                $cleaned_html .= $root->asXml();
            }
        } else {
            $cleaned_html = (string) $xml;
        }
        return $processor->post($this, $cleaned_html);
    }

    /**
     * Parse an element and its children. Removes bad attributes from the
     * element
     *
     * @param SimpleXMLElement $element
     * @return void
     */
    private function parse(SimpleXMLElement $element)
    {
        foreach ($element->children() as $child) {
            $this->parse($child);
        }
        $attrs = $this->getBadAttributes($element);
        if (count($attrs)) {
            $this->cleanBadAttributes($element, $attrs);
        }
    }

    /**
     * Get all attributes that need to be removed/replaced for this element.
     *
     * @param SimpleXMLElement $element
     * @return Attribute[] an array of attributes
     */
    private function getBadAttributes(SimpleXMLElement $element) : array
    {
        $bad_attrs = [];
        foreach ($element->attributes() as $key => $attr) {
            $cleaner = !$this->hasAllowedAttribute($key)
                // attribute is not in the whitelist, delete it
                ? new Remove($key)
                // attribute is in the whitelist, but maybe its content is not
                // good, so we need to check this out
                : $this->getAllowedAttribute($key);

            // clone is mandatory, because it creates an error with
            // dom_import_simplexml if it's omitted. Some sort of conflict
            // between DOM conversion and simplexml reference, probably...
            $cleaner->setAttribute(clone $attr);
            if (!$cleaner->pass()) {
                $bad_attrs[] = $cleaner;
            }
        }
        return $bad_attrs;
    }

    /**
     * Removes or replaces bad attributes for this element
     *
     * @param SimpleXMLElement $element
     * @param Attribute[] $attrs bad attributes to process
     * @return void
     */
    private function cleanBadAttributes(SimpleXMLElement $element, array $attrs)
    {
        $dom = dom_import_simplexml($element);
        foreach ($attrs as $attr) {
            $attr->clean($dom);
        }
    }
}
