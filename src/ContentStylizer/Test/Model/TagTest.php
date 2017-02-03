<?php

namespace ContentStylizer\Test\Model;

use ContentStylizer\Model\Tag as Model;
use PHPUnit_Framework_TestCase;
use stdClass;

/**
 * Test tag model
 */
class TagTest extends PHPUnit_Framework_TestCase
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
        $type = 'paragraph';

        $this->assertInstanceOf(Model::class, $this->model->setType($type));
        $this->assertSame($type, $this->model->getType());
    }

    /**
     * Test beginning
     *
     * @param string|callable $input  input
     * @param string|null     $output output
     * @param array|null      $params params
     *
     * @dataProvider beginningProvider
     */
    public function testBeginning($input, $output = null, array $params = null)
    {
        $this->model
            ->setBeginning($input)
        ;

        if (!isset($output)) {
            $output = $input;
        }
        $this->assertSame($output, $this->model->getBeginning(isset($params) ? (object) $params : null));
    }

    /**
     * Test end
     *
     * @param string|callable $input  input
     * @param string|null     $output output
     * @param array|null      $params params
     *
     * @dataProvider endProvider
     */
    public function testEnd($input, $output = null, array $params = null)
    {
        $this->model
            ->setEnd($input)
        ;

        if (!isset($output)) {
            $output = $input;
        }
        $this->assertSame($output, $this->model->getEnd(isset($params) ? (object) $params : null));
    }

    /**
     * Beginning provider
     *
     * @return array
     */
    public function beginningProvider()
    {
        return [
            [
                '<i>',
            ],
            [
                '<u>',
                null,
                [
                    'unused' => 'param',
                ],
            ],
            [
                function (stdClass $params) {
                    return '<h' . $params->level . '>';
                },
                '<h2>',
                [
                    'level' => 2,
                ],
            ]
        ];
    }

    /**
     * End provider
     *
     * @return array
     */
    public function endProvider()
    {
        return [
            [
                '</i>',
            ],
            [
                '</u>',
                null,
                [
                    'unused' => 'param',
                ],
            ],
            [
                function (stdClass $params) {
                    return '</h' . $params->level . '>';
                },
                '</h4>',
                [
                    'level' => 4,
                ],
            ]
        ];
    }
}
