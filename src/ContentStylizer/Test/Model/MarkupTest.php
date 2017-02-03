<?php

namespace ContentStylizer\Test\Model;

use ContentStylizer\Model\Markup as Model;
use ContentStylizer\Model\Tag;
use PHPUnit_Framework_TestCase;
use stdClass;

/**
 * Test markup model
 */
class MarkupTest extends PHPUnit_Framework_TestCase
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
     * Test tag
     */
    public function testTag()
    {
        $tag = new Tag();

        $this->assertInstanceOf(Model::class, $this->model->setTag($tag));
        $this->assertSame($tag, $this->model->getTag());
    }

    /**
     * Test length
     */
    public function testLength()
    {
        $length = 2;

        $this->assertInstanceOf(Model::class, $this->model->setLength($length));
        $this->assertSame($length, $this->model->getLength());
    }

    /**
     * Test offset
     */
    public function testOffset()
    {
        $offset = 6;

        $this->assertInstanceOf(Model::class, $this->model->setOffset($offset));
        $this->assertSame($offset, $this->model->getOffset());
    }

    /**
     * Test params
     */
    public function testParams()
    {
        $params = [
            'paramA' => 1,
            'paramB' => 2,
        ];

        foreach ($params as $key => $value) {
            $this->assertInstanceOf(Model::class, $this->model->addParam($key, $value));
        }
        $count = 0;
        $paramsObject = $this->model->getParams();
        foreach ($paramsObject as $key => $value) {
            $this->assertArrayHasKey($key, $params);
            $this->assertSame($value, $params[$key]);
            $count++;
        }
        $this->assertSame(count($params), $count);
    }

    /**
     * Test get position
     */
    public function testGetPosition()
    {
        $offset = 2;
        $length = 3;
        $this->model
            ->setOffset($offset)
            ->setLength($length)
        ;

        $this->assertSame($offset, $this->model->getPositionBeginning());
        $this->assertSame($offset + $length, $this->model->getPositionEnd());
    }

    /**
     * Test get tag beginning
     *
     * @param string|callable $input  input
     * @param string|null     $output output
     * @param array           $params params
     *
     * @dataProvider getTagBeginningProvider
     */
    public function testGetTagBeginning($input, $output = null, array $params = [])
    {
        $tag = new Tag();
        $tag->setBeginning($input);
        $this->model->setTag($tag);
        foreach ($params as $key => $value) {
            $this->model->addParam($key, $value);
        }

        if (!isset($output)) {
            $output = $input;
        }
        $this->assertSame($output, $this->model->getTagBeginning());
    }

    /**
     * Test get tag end
     *
     * @param string|callable $input  input
     * @param string|null     $output output
     * @param array           $params params
     *
     * @dataProvider getTagEndProvider
     */
    public function testGetTagEnd($input, $output = null, array $params = [])
    {
        $tag = new Tag();
        $tag->setEnd($input);
        $this->model->setTag($tag);
        foreach ($params as $key => $value) {
            $this->model->addParam($key, $value);
        }

        if (!isset($output)) {
            $output = $input;
        }
        $this->assertSame($output, $this->model->getTagEnd());
    }

    /**
     * Get tag beginning provider
     *
     * @return array
     */
    public function getTagBeginningProvider()
    {
        return [
            [
                '<i>',
            ],
            [
                function (stdClass $params) {
                    return '<a href="' . $params->uri . '">';
                },
                '<a href="http://www.svd.se">',
                [
                    'uri' => 'http://www.svd.se',
                ],
            ],
        ];
    }

    /**
     * Get tag end provider
     *
     * @return array
     */
    public function getTagEndProvider()
    {
        return [
            [
                '</i>',
            ],
            [
                function (stdClass $params) {
                    return '</h' . $params->level . '>';
                },
                '</h3>',
                [
                    'level' => 3,
                ],
            ],
        ];
    }
}
