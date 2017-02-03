<?php

namespace ContentStylizer\Test\Model;

use ContentStylizer\Model\Markup;
use ContentStylizer\Model\Occurence as Model;
use ContentStylizer\Model\Tag;
use PHPUnit_Framework_TestCase;

/**
 * Test occurence model
 */
class OccurenceTest extends PHPUnit_Framework_TestCase
{
    /** @var Model */
    protected $model;

    /**
     * Set up
     */
    protected function setUp()
    {
        $this->model = new Model();
    }

    /**
     * Test type
     */
    public function testType()
    {
        $type = 'anchor';

        $this->assertInstanceOf(Model::class, $this->model->setType($type));
        $this->assertSame($type, $this->model->getType());
    }

    /**
     * Test markup
     */
    public function testMarkup()
    {
        $markup = new Markup();

        $this->assertInstanceOf(Model::class, $this->model->setMarkup($markup));
        $this->assertSame($markup, $this->model->getMarkup());
    }

    /**
     * Test get HTML
     *
     * @param Markup $markup markup
     * @param string $type   type
     * @param string $output output
     *
     * @dataProvider getHtmlProvider
     */
    public function testGetHtml($markup, $type, $output)
    {
        $this->model
            ->setType($type)
            ->setMarkup($markup)
        ;

        $this->assertSame($output, $this->model->getHtml());
    }

    /**
     * Get HTML provider
     *
     * @return array
     */
    public function getHtmlProvider()
    {
        $tag = new Tag();
        $tag
            ->setBeginning('<strong>')
            ->setEnd('</strong>')
        ;

        $markup = new Markup();
        $markup->setTag($tag);

        return [
            [
                $markup,
                Model::TYPE_BEGINNING,
                '<strong>',
            ],
            [
                $markup,
                Model::TYPE_END,
                '</strong>',
            ],
        ];
    }
}
