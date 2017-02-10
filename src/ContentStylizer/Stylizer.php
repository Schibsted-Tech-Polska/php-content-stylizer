<?php

namespace ContentStylizer;

use ContentStylizer\Model\Markup;
use ContentStylizer\Model\Occurence;
use ContentStylizer\Model\Tag;
use stdClass;

/**
 * Stylizer
 */
class Stylizer
{
    /** @var Tag[] */
    private $tags = [];

    /**
     * Constructor
     *
     * @param array $supportedTags supported tags
     */
    public function __construct(array $supportedTags = [])
    {
        foreach ($supportedTags as $record) {
            $record = (object) $record;
            if (!empty($record->type) && isset($record->beginning)) {
                $this->addTag($record->type, $record->beginning, isset($record->end) ? $record->end : null);
            }
        }
    }

    /**
     * Add tag
     *
     * @param string               $type      type
     * @param string|callable      $beginning beginning
     * @param string|callable|null $end       end
     *
     * @return self
     */
    public function addTag($type, $beginning, $end = null)
    {
        $tag = new Tag();
        $tag
            ->setType($type)
            ->setBeginning($beginning)
        ;
        if (isset($end)) {
            $tag->setEnd($end);
        }
        $this->tags[$type] = $tag;

        return $this;
    }

    /**
     * Get tag
     *
     * @param string $type type
     *
     * @return Tag|null
     */
    public function getTag($type)
    {
        $tag = $this->hasTag($type) ? $this->tags[$type] : null;

        return $tag;
    }

    /**
     * Has tag
     *
     * @param string $type type
     *
     * @return bool
     */
    public function hasTag($type)
    {
        $hasTag = array_key_exists($type, $this->tags);

        return $hasTag;
    }

    /**
     * Get HTML
     *
     * @param string         $text        text
     * @param array|stdClass $markupsData markups data
     *
     * @return string
     */
    public function getHtml($text, array $markupsData = [])
    {
        if (empty($text)) {
            return $text;
        }

        $markups = $this->getMarkups($markupsData);
        $markups = $this->addLineBreakMarkups($text, $markups);
        $occurencesByPos = $this->getOccurencesByPositions($markups);
        $htmlParts = $this->getHtmlParts($text, $occurencesByPos);
        $html = implode('', $htmlParts);

        return $html;
    }

    /**
     * Get markups
     *
     * @param array $markupsData markups data
     *
     * @return Markup[]
     */
    private function getMarkups(array $markupsData)
    {
        $markups = [];
        $configFields = [
            'length',
            'offset',
            'type',
        ];
        foreach ($markupsData as $record) {
            $record = (object) $record;
            if (isset($record->type) && isset($record->offset) && array_key_exists($record->type, $this->tags)) {
                $markup = new Markup();
                $markup
                    ->setTag($this->tags[$record->type])
                    ->setLength(empty($record->length) ? 0 : (int) $record->length)
                    ->setOffset((int) $record->offset)
                ;
                foreach ($record as $key => $value) {
                    if (!in_array($key, $configFields)) {
                        $markup->addParam($key, $value);
                    }
                }
                $markups[] = $markup;
            }
        }
        $markups = $this->fixMarkupsInheritance($this->sortMarkups($markups));

        return $markups;
    }

    /**
     * Sort markups
     *
     * @param Markup[] $markups markups
     *
     * @return Markup[]
     */
    private function sortMarkups(array $markups)
    {
        usort($markups, [
            $this,
            'compareMarkups',
        ]);

        return $markups;
    }

    /**
     * Compare markups
     *
     * @param Markup $markupA markup A
     * @param Markup $markupB markup B
     *
     * @return int
     */
    private function compareMarkups(Markup $markupA, Markup $markupB)
    {
        // Sorting by opening position ascending
        $primarySortResult = $markupA->getOffset() - $markupB->getOffset();
        if ($primarySortResult != 0) {
            return $primarySortResult;
        }

        // Sorting by length (closing position) descending
        $secondarySortResult = $markupB->getLength() - $markupA->getLength();

        return $secondarySortResult;
    }

    /**
     * Fix markups inheritance
     *
     * @param Markup[] $markups markups
     *
     * @return Markup[]
     */
    private function fixMarkupsInheritance(array $markups)
    {
        /** @var Markup[] $markupStack */
        $markupStack = [];
        for ($i = 0; $i < count($markups); $i++) {
            $markup = $markups[$i];

            // Remove all already "finished" markups from inheritance stack
            while (false !== $lastMarkup = reset($markupStack)) {
                if ($lastMarkup->getPositionEnd() <= $markup->getPositionBeginning()) {
                    array_shift($markupStack);
                } else {
                    break;
                }
            }

            // Divides markup if it's longer than its parent
            if ($lastMarkup !== false && $markup->getPositionEnd() > $lastMarkup->getPositionEnd()) {
                $divisionPosition = $lastMarkup->getPositionEnd();

                // Create second part of divided markup and put it in correct place inside an array
                $secondPartMarkup = new Markup();
                $secondPartMarkup
                    ->setTag($markup->getTag())
                    ->setParams($markup->getParams())
                    ->setOffset($divisionPosition)
                    ->setLength($markup->getPositionEnd() - $divisionPosition)
                ;

                for ($j = $i + 1, $length = count($markups); $j < $length; $j++) {
                    if ($this->compareMarkups($markups[$j], $secondPartMarkup) >= 0) {
                        array_splice($markups, $j, 0, [
                            $secondPartMarkup,
                        ]);
                        unset($secondPartMarkup);
                        break;
                    }
                }
                if (isset($secondPartMarkup)) {
                    array_push($markups, $secondPartMarkup);
                    unset($secondPartMarkup);
                }

                // Cut first part of markup
                $markup->setLength($divisionPosition - $markup->getPositionBeginning());
            }

            // Puts current markup on inheritance stack
            array_unshift($markupStack, $markup);
        }

        return $markups;
    }

    /**
     * Add line break markups
     *
     * @param string   $text    text
     * @param Markup[] $markups markups
     *
     * @return Markup[]
     */
    private function addLineBreakMarkups($text, array $markups)
    {
        if ($this->hasTag('br')) {
            $lineBreakTag = $this->getTag('br');

            $offset = 0;
            while (false !== $position = mb_strpos($text, PHP_EOL, $offset)) {
                $markup = new Markup();
                $markup
                    ->setTag($lineBreakTag)
                    ->setLength(0)
                    ->setOffset($position)
                ;
                $markups[] = $markup;
                $offset = $position + 1;
            }
        }

        return $markups;
    }

    /**
     * Get occurences
     *
     * @param Markup[] $markups markups
     *
     * @return array
     */
    private function getOccurencesByPositions(array $markups)
    {
        $occurencesByPos = [];
        foreach ($markups as $markup) {
            $beginning = $markup->getPositionBeginning();
            $end = $markup->getPositionEnd();
            if ($beginning == $end) {
                $occurencesByPos = $this->addOccurence(
                    $occurencesByPos,
                    $beginning,
                    Occurence::TYPE_SINGLETON,
                    $markup
                );
            } else {
                $occurencesByPos = $this->addOccurence(
                    $occurencesByPos,
                    $beginning,
                    Occurence::TYPE_BEGINNING,
                    $markup
                );
                $occurencesByPos = $this->addOccurence(
                    $occurencesByPos,
                    $end,
                    Occurence::TYPE_END,
                    $markup
                );
            }
        }

        $occurencesByPos = $this->sortOccurences($occurencesByPos);

        return $occurencesByPos;
    }

    /**
     * Add occurence
     *
     * @param array  $occurencesByPos occurences by positions
     * @param int    $position        position
     * @param string $type            type
     * @param Markup $markup          markup
     *
     * @return array
     */
    private function addOccurence(array $occurencesByPos, $position, $type, Markup $markup)
    {
        if (!array_key_exists($position, $occurencesByPos)) {
            $occurencesByPos[$position] = [];
        }

        $occurence = new Occurence();
        $occurence
            ->setType($type)
            ->setMarkup($markup)
        ;
        $occurencesByPos[$position][] = $occurence;

        return $occurencesByPos;
    }

    /**
     * Sort occurences
     *
     * @param array $occurencesByPos occurences by positions
     *
     * @return array
     */
    private function sortOccurences(array $occurencesByPos)
    {
        foreach ($occurencesByPos as $no => $group) {
            if (count($group) > 0) {
                usort($occurencesByPos[$no], function (Occurence $occurenceA, Occurence $occurenceB) {
                    $primarySortResult = $occurenceA->getType() - $occurenceB->getType();
                    if ($primarySortResult != 0) {
                        return $primarySortResult;
                    }

                    switch ($occurenceA->getType()) {
                        case Occurence::TYPE_BEGINNING:
                            // First should be a tag which will be closed as last one
                            return $occurenceB->getMarkup()->getLength() - $occurenceA->getMarkup()->getLength();

                        case Occurence::TYPE_END:
                            // First should be a tag which was opened as last one
                            return $occurenceB->getMarkup()->getOffset() - $occurenceA->getMarkup()->getOffset();

                        case Occurence::TYPE_SINGLETON:
                        default:
                            return 0;
                    }
                });
            }
        }
        ksort($occurencesByPos);

        return $occurencesByPos;
    }

    /**
     * Get HTML parts
     *
     * @param string $text            text
     * @param array  $occurencesByPos occurences by positions
     *
     * @return array
     */
    private function getHtmlParts($text, array $occurencesByPos)
    {
        $offset = 0;
        $position = 0;
        $htmlParts = [];

        /** @var Occurence[] $occurences */
        foreach ($occurencesByPos as $position => $occurences) {
            if ($position > 0) {
                $htmlParts[] = $this->escape(mb_substr($text, $offset, $position - $offset));
                $offset = $position;
            }
            foreach ($occurences as $occurence) {
                $htmlParts[] = $occurence->getHtml();
            }
        }

        if ($position < mb_strlen($text)) {
            $htmlParts[] = $this->escape(mb_substr($text, $position));
        }

        return $htmlParts;
    }

    /**
     * Escape
     *
     * @param string $text text
     *
     * @return string
     */
    private function escape($text)
    {
        $escapedText = htmlspecialchars($text, ENT_QUOTES);

        return $escapedText;
    }
}
