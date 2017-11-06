<?php

namespace Lampager\Tests;

class ProcessorTest extends TestCase
{
    public static $rows = [
        ['id' => '1', 'updated_at' => '2017-01-01 10:00:00'],
        ['id' => '3', 'updated_at' => '2017-01-01 10:00:00'],
        ['id' => '5', 'updated_at' => '2017-01-01 10:00:00'],
        ['id' => '2', 'updated_at' => '2017-01-01 11:00:00'],
        ['id' => '4', 'updated_at' => '2017-01-01 11:00:00'],
    ];

    /**
     * @param $expected
     * @param $actual
     */
    protected function assertResultSame($expected, $actual)
    {
        $this->assertSame(
            json_decode(json_encode($expected), true),
            json_decode(json_encode($actual), true)
        );
    }

    /**
     * @test
     */
    public function testAscendingForwardStartInclusive()
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => '1', 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => '3', 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => '5', 'updated_at' => '2017-01-01 10:00:00'],
                ],
                'hasPrevious' => null,
                'previousCursor' => null,
                'hasNext' => true,
                'nextCursor' => ['updated_at' => '2017-01-01 11:00:00', 'id' => '2'],
            ],
            (new StubPaginator('posts'))
                ->forward()->limit(3)
                ->orderBy('updated_at')
                ->orderBy('id')
                ->seekable()
                ->paginate()
        );
    }

    /**
     * @test
     */
    public function testAscendingForwardStartExclusive()
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => '1', 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => '3', 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => '5', 'updated_at' => '2017-01-01 10:00:00'],
                ],
                'hasPrevious' => null,
                'previousCursor' => null,
                'hasNext' => true,
                'nextCursor' => ['updated_at' => '2017-01-01 10:00:00', 'id' => '5'],
            ],
            (new StubPaginator('posts'))
                ->forward()->limit(3)
                ->orderBy('updated_at')
                ->orderBy('id')
                ->seekable()
                ->exclusive()
                ->paginate()
        );
    }

    /**
     * @test
     */
    public function testAscendingForwardInclusive()
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => '3', 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => '5', 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => '2', 'updated_at' => '2017-01-01 11:00:00'],
                ],
                'hasPrevious' => true,
                'previousCursor' => ['updated_at' => '2017-01-01 10:00:00', 'id' => '1'],
                'hasNext' => true,
                'nextCursor' => ['updated_at' => '2017-01-01 11:00:00', 'id' => '4'],
            ],
            (new StubPaginator('posts'))
                ->forward()->limit(3)
                ->orderBy('updated_at')
                ->orderBy('id')
                ->seekable()
                ->paginate(['id' => '3', 'updated_at' => '2017-01-01 10:00:00'])
        );
    }

    /**
     * @test
     */
    public function testAscendingForwardExclusive()
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => '5', 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => '2', 'updated_at' => '2017-01-01 11:00:00'],
                    ['id' => '4', 'updated_at' => '2017-01-01 11:00:00'],
                ],
                'hasPrevious' => true,
                'previousCursor' => ['updated_at' => '2017-01-01 10:00:00', 'id' => '5'],
                'hasNext' => false,
                'nextCursor' => null,
            ],
            (new StubPaginator('posts'))
                ->forward()->limit(3)
                ->orderBy('updated_at')
                ->orderBy('id')
                ->seekable()
                ->exclusive()
                ->paginate(['id' => '3', 'updated_at' => '2017-01-01 10:00:00'])
        );
    }

    /**
     * @test
     */
    public function testAscendingBackwardStartInclusive()
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => '5', 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => '2', 'updated_at' => '2017-01-01 11:00:00'],
                    ['id' => '4', 'updated_at' => '2017-01-01 11:00:00'],
                ],
                'hasPrevious' => true,
                'previousCursor' => ['updated_at' => '2017-01-01 10:00:00', 'id' => '3'],
                'hasNext' => null,
                'nextCursor' => null,
            ],
            (new StubPaginator('posts'))
                ->backward()->limit(3)
                ->orderBy('updated_at')
                ->orderBy('id')
                ->seekable()
                ->paginate()
        );
    }

    /**
     * @test
     */
    public function testAscendingBackwardStartExclusive()
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => '5', 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => '2', 'updated_at' => '2017-01-01 11:00:00'],
                    ['id' => '4', 'updated_at' => '2017-01-01 11:00:00'],
                ],
                'hasPrevious' => true,
                'previousCursor' => ['updated_at' => '2017-01-01 10:00:00', 'id' => '5'],
                'hasNext' => null,
                'nextCursor' => null,
            ],
            (new StubPaginator('posts'))
                ->backward()->limit(3)
                ->orderBy('updated_at')
                ->orderBy('id')
                ->seekable()
                ->exclusive()
                ->paginate()
        );
    }

    /**
     * @test
     */
    public function testAscendingBackwardInclusive()
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => '1', 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => '3', 'updated_at' => '2017-01-01 10:00:00'],
                ],
                'hasPrevious' => false,
                'previousCursor' => null,
                'hasNext' => true,
                'nextCursor' => ['updated_at' => '2017-01-01 10:00:00', 'id' => '5'],
            ],
            (new StubPaginator('posts'))
                ->backward()->limit(3)
                ->orderBy('updated_at')
                ->orderBy('id')
                ->seekable()
                ->paginate(['id' => '3', 'updated_at' => '2017-01-01 10:00:00'])
        );
    }

    /**
     * @test
     */
    public function testAscendingBackwardExclusive()
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => '1', 'updated_at' => '2017-01-01 10:00:00'],
                ],
                'hasPrevious' => false,
                'previousCursor' => null,
                'hasNext' => true,
                'nextCursor' => ['updated_at' => '2017-01-01 10:00:00', 'id' => '1'],
            ],
            (new StubPaginator('posts'))
                ->backward()->limit(3)
                ->orderBy('updated_at')
                ->orderBy('id')
                ->seekable()
                ->exclusive()
                ->paginate(['id' => '3', 'updated_at' => '2017-01-01 10:00:00'])
        );
    }

    /**
     * @test
     */
    public function testDescendingForwardStartInclusive()
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => '4', 'updated_at' => '2017-01-01 11:00:00'],
                    ['id' => '2', 'updated_at' => '2017-01-01 11:00:00'],
                    ['id' => '5', 'updated_at' => '2017-01-01 10:00:00'],
                ],
                'hasPrevious' => null,
                'previousCursor' => null,
                'hasNext' => true,
                'nextCursor' => ['updated_at' => '2017-01-01 10:00:00', 'id' => '3'],
            ],
            (new StubPaginator('posts'))
                ->forward()->limit(3)
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->seekable()
                ->paginate()
        );
    }

    /**
     * @test
     */
    public function testDescendingForwardStartExclusive()
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => '4', 'updated_at' => '2017-01-01 11:00:00'],
                    ['id' => '2', 'updated_at' => '2017-01-01 11:00:00'],
                    ['id' => '5', 'updated_at' => '2017-01-01 10:00:00'],
                ],
                'hasPrevious' => null,
                'previousCursor' => null,
                'hasNext' => true,
                'nextCursor' => ['updated_at' => '2017-01-01 10:00:00', 'id' => '5'],
            ],
            (new StubPaginator('posts'))
                ->forward()->limit(3)
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->seekable()
                ->exclusive()
                ->paginate()
        );
    }

    /**
     * @test
     */
    public function testDescendingForwardInclusive()
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => '3', 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => '1', 'updated_at' => '2017-01-01 10:00:00'],
                ],
                'hasPrevious' => true,
                'previousCursor' => ['updated_at' => '2017-01-01 10:00:00', 'id' => '5'],
                'hasNext' => false,
                'nextCursor' => null,
            ],
            (new StubPaginator('posts'))
                ->forward()->limit(3)
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->seekable()
                ->paginate(['id' => '3', 'updated_at' => '2017-01-01 10:00:00'])
        );
    }

    /**
     * @test
     */
    public function testDescendingForwardExclusive()
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => '1', 'updated_at' => '2017-01-01 10:00:00'],
                ],
                'hasPrevious' => true,
                'previousCursor' => ['updated_at' => '2017-01-01 10:00:00', 'id' => '1'],
                'hasNext' => false,
                'nextCursor' => null,
            ],
            (new StubPaginator('posts'))
                ->forward()->limit(3)
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->seekable()
                ->exclusive()
                ->paginate(['id' => '3', 'updated_at' => '2017-01-01 10:00:00'])
        );
    }

    /**
     * @test
     */
    public function testDescendingBackwardStartInclusive()
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => '5', 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => '3', 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => '1', 'updated_at' => '2017-01-01 10:00:00'],
                ],
                'hasPrevious' => true,
                'previousCursor' => ['updated_at' => '2017-01-01 11:00:00', 'id' => '2'],
                'hasNext' => null,
                'nextCursor' => null,
            ],
            (new StubPaginator('posts'))
                ->backward()->limit(3)
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->seekable()
                ->paginate()
        );
    }

    /**
     * @test
     */
    public function testDescendingBackwardStartExclusive()
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => '5', 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => '3', 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => '1', 'updated_at' => '2017-01-01 10:00:00'],
                ],
                'hasPrevious' => true,
                'previousCursor' => ['updated_at' => '2017-01-01 10:00:00', 'id' => '5'],
                'hasNext' => null,
                'nextCursor' => null,
            ],
            (new StubPaginator('posts'))
                ->backward()->limit(3)
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->seekable()
                ->exclusive()
                ->paginate()
        );
    }

    /**
     * @test
     */
    public function testDescendingBackwardInclusive()
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => '2', 'updated_at' => '2017-01-01 11:00:00'],
                    ['id' => '5', 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => '3', 'updated_at' => '2017-01-01 10:00:00'],
                ],
                'hasPrevious' => true,
                'previousCursor' => ['updated_at' => '2017-01-01 11:00:00', 'id' => '4'],
                'hasNext' => true,
                'nextCursor' => ['updated_at' => '2017-01-01 10:00:00', 'id' => '1'],
            ],
            (new StubPaginator('posts'))
                ->backward()->limit(3)
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->seekable()
                ->paginate(['id' => '3', 'updated_at' => '2017-01-01 10:00:00'])
        );
    }

    /**
     * @test
     */
    public function testDescendingBackwardExclusive()
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => '4', 'updated_at' => '2017-01-01 11:00:00'],
                    ['id' => '2', 'updated_at' => '2017-01-01 11:00:00'],
                    ['id' => '5', 'updated_at' => '2017-01-01 10:00:00'],
                ],
                'hasPrevious' => false,
                'previousCursor' => null,
                'hasNext' => true,
                'nextCursor' => ['updated_at' => '2017-01-01 10:00:00', 'id' => '5'],
            ],
            (new StubPaginator('posts'))
                ->backward()->limit(3)
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->seekable()
                ->exclusive()
                ->paginate(['id' => '3', 'updated_at' => '2017-01-01 10:00:00'])
        );
    }
}
