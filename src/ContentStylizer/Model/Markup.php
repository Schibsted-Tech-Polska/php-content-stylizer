<?php

namespace ContentStylizer\Model;

use stdClass;

/**
 * Markup model
 */
class Markup
{
    /** @var Tag */
    private $tag;

    /** @var int */
    private $length = 0;

    /** @var int */
    private $offset = 0;

    /** @var stdClass */
    private $params;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->params = new stdClass();
    }

    /**
     * Get tag
     *
     * @return Tag
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set tag
     *
     * @param Tag $tag tag
     *
     * @return self
     */
    public function setTag(Tag $tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get length
     *
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * Set length
     *
     * @param int $length length
     *
     * @return self
     */
    public function setLength($length)
    {
        $this->length = $length;

        return $this;
    }

    /**
     * Get offset
     *
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Set offset
     *
     * @param int $offset offset
     *
     * @return self
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * Get position beginning
     *
     * @return int
     */
    public function getPositionBeginning()
    {
        return $this->getOffset();
    }

    /**
     * Get position end
     *
     * @return int
     */
    public function getPositionEnd()
    {
        return $this->getOffset() + $this->getLength();
    }

    /**
     * Add param
     *
     * @param string $key   key
     * @param mixed  $value value
     *
     * @return self
     */
    public function addParam($key, $value)
    {
        $this->params->$key = $value;

        return $this;
    }

    /**
     * Get params
     *
     * @return stdClass
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Set params
     *
     * @param stdClass $params params
     *
     * @return self
     */
    public function setParams(stdClass $params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * Get tag beginning
     *
     * @return int
     */
    public function getTagBeginning()
    {
        $html = $this
            ->getTag()
            ->getBeginning($this->getParams())
        ;

        return $html;
    }

    /**
     * Get tag end
     *
     * @return int
     */
    public function getTagEnd()
    {
        $html = $this
            ->getTag()
            ->getEnd($this->getParams())
        ;

        return $html;
    }
}
