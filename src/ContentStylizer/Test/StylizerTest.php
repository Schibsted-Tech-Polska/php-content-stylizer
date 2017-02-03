<?php

namespace ContentStylizer\Test;

use ContentStylizer\Model\Tag;
use ContentStylizer\Stylizer;
use PHPUnit_Framework_TestCase;
use stdClass;

/**
 * Test stylizer
 */
class StylizerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test tags support
     */
    public function testTagsSupport()
    {
        $stylizer = new Stylizer();

        $stylizer
            ->addTag('strong', '<strong>', '</strong>')
            ->addTag('anchor', function (stdClass $params) {
                return '<a href="' . $params->uri . '" target="_blank">';
            }, '</a>')
            ->addTag('break', '<br>')
        ;

        $this->assertTrue($stylizer->hasTag('strong'));
        $this->assertTrue($stylizer->hasTag('anchor'));
        $this->assertTrue($stylizer->hasTag('break'));
        $this->assertFalse($stylizer->hasTag('underline'));

        $this->assertInstanceOf(Tag::class, $stylizer->getTag('strong'));
        $this->assertNull($stylizer->getTag('underline'));
    }

    /**
     * Test get HTML
     *
     * @param array       $tags    tags
     * @param array       $markups markups
     * @param string      $input   input
     * @param string|null $output  output
     *
     * @dataProvider getHtmlProvider
     */
    public function testGetHtml(array $tags, array $markups, $input, $output = null)
    {
        $stylizer = new Stylizer($tags);

        if (!isset($output)) {
            $output = $input;
        }
        $this->assertEquals($output, $stylizer->getHtml($input, $markups));
    }

    /**
     * Get HTML provider
     *
     * @return array
     */
    public function getHtmlProvider()
    {
        $tags1 = [
            [
                'beginning' => '<br>',
                'type' => 'br',
            ],
        ];

        return [
            [
                [],
                [],
                'Test string',
            ],
            [
                [],
                [],
                "Test\nstring",
            ],
            [
                $tags1,
                [],
                "Test\nstring",
                "Test<br>\nstring",
            ],
        ];

        // @TODO: Add more advanced samples with many tags!
    }
}
