<?php

namespace ContentStylizer\Model;

/**
 * Occurence model
 */
class Occurence
{
    /** @const int */
    const TYPE_BEGINNING = 3;

    /** @const int */
    const TYPE_END = 1;

    /** @const int */
    const TYPE_SINGLETON = 2;

    /** @var string */
    private $type;

    /** @var Markup */
    private $markup;

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param string $type type
     *
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get markup
     *
     * @return Markup
     */
    public function getMarkup()
    {
        return $this->markup;
    }

    /**
     * Set markup
     *
     * @param Markup $markup markup
     *
     * @return self
     */
    public function setMarkup(Markup $markup)
    {
        $this->markup = $markup;

        return $this;
    }

    /**
     * Get HTML
     *
     * @return string
     */
    public function getHtml()
    {
        $markup = $this->getMarkup();
        $html = $this->getType() == self::TYPE_END ? $markup->getTagEnd() : $markup->getTagBeginning();

        return $html;
    }
}
