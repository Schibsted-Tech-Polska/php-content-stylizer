<?php

namespace ContentStylizer\Model;

use stdClass;

/**
 * Tag model
 */
class Tag
{
    /** @var string */
    private $type;

    /** @var string|callable */
    private $beginning;

    /** @var string|callable|null */
    private $end;

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
     * Get beginning
     *
     * @param stdClass|null $params params
     *
     * @return string
     */
    public function getBeginning(stdClass $params = null)
    {
        if (is_callable($this->beginning)) {
            $function = $this->beginning;
            $beginning = $function(isset($params) ? $params : new stdClass());
        } else {
            $beginning = $this->beginning;
        }

        return $beginning;
    }

    /**
     * Set beginning
     *
     * @param string|callable $beginning beginning
     *
     * @return self
     */
    public function setBeginning($beginning)
    {
        $this->beginning = $beginning;

        return $this;
    }

    /**
     * Get end
     *
     * @param stdClass|null $params params
     *
     * @return string
     */
    public function getEnd(stdClass $params = null)
    {
        if (is_callable($this->end)) {
            $function = $this->end;
            $end = $function(isset($params) ? $params : new stdClass());
        } else {
            $end = $this->end;
        }

        return $end;
    }

    /**
     * Set end
     *
     * @param string|callable|null $end end
     *
     * @return self
     */
    public function setEnd($end)
    {
        $this->end = $end;

        return $this;
    }
}
