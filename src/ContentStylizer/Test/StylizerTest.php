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
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getHtmlProvider()
    {
        $tags1 = [
            [
                'beginning' => '<br>',
                'type' => 'br',
            ],
        ];

        $tags2 = [
            [
                'beginning' => '<br>',
                'type' => 'br',
            ],
            [
                'beginning' => function (stdClass $params) {
                    return '<a href="' . $params->uri . '" target="_blank">';
                },
                'end' => '</a>',
                'type' => 'link:external',
            ],
            [
                'beginning' => function (stdClass $params) {
                    $contentParts = explode(':', $params->uri);
                    $contentType = $contentParts[0];
                    $contentId = $contentParts[1];

                    switch ($contentType) {
                        case 'author':
                            $href = '/av/' . $contentId;
                            break;

                        case 'topic':
                            $href = '/om/' . $contentId;
                            break;

                        default:
                            $href = '/' . $contentId;
                            break;
                    }

                    return '<a href="' . $href . '">';
                },
                'end' => '</a>',
                'type' => 'link:internal',
            ],
            [
                'beginning' => '<span class="Anfang">',
                'end' => '</span>',
                'type' => 'style:anfang',
            ],
            [
                'beginning' => '<em>',
                'end' => '</em>',
                'type' => 'style:em',
            ],
            [
                'beginning' => '<span class="u-highlightText">',
                'end' => '</span>',
                'type' => 'style:highlight',
            ],
            [
                'beginning' => '<strong>',
                'end' => '</strong>',
                'type' => 'style:strong',
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
            [
                $tags2,
                [
                    [
                        'length' => 6,
                        'offset' => 26,
                        'type' => 'style:highlight',
                    ],
                    [
                        'length' => 10,
                        'offset' => 46,
                        'type' => 'style:em',
                    ],
                    [
                        'length' => 16,
                        'offset' => 49,
                        'type' => 'link:external',
                        'uri' => 'http://www.example.com',
                    ],
                    [
                        'length' => 8,
                        'offset' => 57,
                        'type' => 'style:strong',
                    ],
                    [
                        'length' => 8,
                        'offset' => 70,
                        'type' => 'link:internal',
                        'uri' => 'article:9336dcbc-db7d-3985-8ea2-cd691897dde7',
                    ],
                ],
                "This is sample text which should help to test if content stylizer\nfor articles works as it has to.",
                'This is sample text which <span class="u-highlightText">should</span> help to test ' .
                    '<em>if <a href="http://www.example.com" target="_blank">content</a></em>' .
                    '<a href="http://www.example.com" target="_blank"> <strong>stylizer</strong></a><br>' . "\n" .
                    'for <a href="/9336dcbc-db7d-3985-8ea2-cd691897dde7">articles</a> works as it has to.',
            ],
            [
                $tags2,
                [
                    [
                        'length' => 4,
                        'offset' => 0,
                        'type' => 'style:strong',
                    ],
                    [
                        'length' => 7,
                        'offset' => 0,
                        'type' => 'style:em',
                    ],
                    [
                        'length' => 18,
                        'offset' => 5,
                        'type' => 'style:highlight',
                    ],
                    [
                        'length' => 7,
                        'offset' => 8,
                        'type' => 'style:anfang',
                    ],
                    [
                        'length' => 15,
                        'offset' => 8,
                        'type' => 'style:em',
                    ],
                    [
                        'length' => 11,
                        'offset' => 24,
                        'type' => 'style:em',
                    ],
                    [
                        'length' => 29,
                        'offset' => 6,
                        'type' => 'style:strong',
                    ],
                ],
                'Test of correct markups inheritance.',
                '<em><strong>Test</strong> <span class="u-highlightText">o<strong>f</strong></span></em>' .
                    '<strong><span class="u-highlightText"> <em><span class="Anfang">correct</span> markups</em>' .
                    '</span> <em>inheritance</em></strong>.',
            ],
        ];
    }
}
