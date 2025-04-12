<?php

namespace Lampager\Tests;

use PHPUnit\Framework\Attributes\Test;

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

    #[Test]
    public function testAscendingForwardStartInclusive(): void
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => $this->number(1), 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => $this->number(3), 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => $this->number(5), 'updated_at' => '2017-01-01 10:00:00'],
                ],
                'hasPrevious' => null,
                'previousCursor' => null,
                'hasNext' => true,
                'nextCursor' => ['updated_at' => '2017-01-01 11:00:00', 'id' => $this->number(2)],
            ],
            (new StubPaginator('posts'))
                ->forward()->limit(3)
                ->orderBy('updated_at')
                ->orderBy('id')
                ->seekable()
                ->paginate()
        );
    }

    #[Test]
    public function testAscendingForwardStartExclusive(): void
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => $this->number(1), 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => $this->number(3), 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => $this->number(5), 'updated_at' => '2017-01-01 10:00:00'],
                ],
                'hasPrevious' => null,
                'previousCursor' => null,
                'hasNext' => true,
                'nextCursor' => ['updated_at' => '2017-01-01 10:00:00', 'id' => $this->number(5)],
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

    #[Test]
    public function testAscendingForwardInclusive(): void
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => $this->number(3), 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => $this->number(5), 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => $this->number(2), 'updated_at' => '2017-01-01 11:00:00'],
                ],
                'hasPrevious' => true,
                'previousCursor' => ['updated_at' => '2017-01-01 10:00:00', 'id' => $this->number(1)],
                'hasNext' => true,
                'nextCursor' => ['updated_at' => '2017-01-01 11:00:00', 'id' => $this->number(4)],
            ],
            (new StubPaginator('posts'))
                ->forward()->limit(3)
                ->orderBy('updated_at')
                ->orderBy('id')
                ->seekable()
                ->paginate(['id' => '3', 'updated_at' => '2017-01-01 10:00:00'])
        );
    }

    #[Test]
    public function testAscendingForwardExclusive(): void
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => $this->number(5), 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => $this->number(2), 'updated_at' => '2017-01-01 11:00:00'],
                    ['id' => $this->number(4), 'updated_at' => '2017-01-01 11:00:00'],
                ],
                'hasPrevious' => true,
                'previousCursor' => ['updated_at' => '2017-01-01 10:00:00', 'id' => $this->number(5)],
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

    #[Test]
    public function testAscendingBackwardStartInclusive(): void
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => $this->number(5), 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => $this->number(2), 'updated_at' => '2017-01-01 11:00:00'],
                    ['id' => $this->number(4), 'updated_at' => '2017-01-01 11:00:00'],
                ],
                'hasPrevious' => true,
                'previousCursor' => ['updated_at' => '2017-01-01 10:00:00', 'id' => $this->number(3)],
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

    #[Test]
    public function testAscendingBackwardStartExclusive(): void
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => $this->number(5), 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => $this->number(2), 'updated_at' => '2017-01-01 11:00:00'],
                    ['id' => $this->number(4), 'updated_at' => '2017-01-01 11:00:00'],
                ],
                'hasPrevious' => true,
                'previousCursor' => ['updated_at' => '2017-01-01 10:00:00', 'id' => $this->number(5)],
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

    #[Test]
    public function testAscendingBackwardInclusive(): void
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => $this->number(1), 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => $this->number(3), 'updated_at' => '2017-01-01 10:00:00'],
                ],
                'hasPrevious' => false,
                'previousCursor' => null,
                'hasNext' => true,
                'nextCursor' => ['updated_at' => '2017-01-01 10:00:00', 'id' => $this->number(5)],
            ],
            (new StubPaginator('posts'))
                ->backward()->limit(3)
                ->orderBy('updated_at')
                ->orderBy('id')
                ->seekable()
                ->paginate(['id' => '3', 'updated_at' => '2017-01-01 10:00:00'])
        );
    }

    #[Test]
    public function testAscendingBackwardExclusive(): void
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => $this->number(1), 'updated_at' => '2017-01-01 10:00:00'],
                ],
                'hasPrevious' => false,
                'previousCursor' => null,
                'hasNext' => true,
                'nextCursor' => ['updated_at' => '2017-01-01 10:00:00', 'id' => $this->number(1)],
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

    #[Test]
    public function testDescendingForwardStartInclusive(): void
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => $this->number(4), 'updated_at' => '2017-01-01 11:00:00'],
                    ['id' => $this->number(2), 'updated_at' => '2017-01-01 11:00:00'],
                    ['id' => $this->number(5), 'updated_at' => '2017-01-01 10:00:00'],
                ],
                'hasPrevious' => null,
                'previousCursor' => null,
                'hasNext' => true,
                'nextCursor' => ['updated_at' => '2017-01-01 10:00:00', 'id' => $this->number(3)],
            ],
            (new StubPaginator('posts'))
                ->forward()->limit(3)
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->seekable()
                ->paginate()
        );
    }

    #[Test]
    public function testDescendingForwardStartExclusive(): void
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => $this->number(4), 'updated_at' => '2017-01-01 11:00:00'],
                    ['id' => $this->number(2), 'updated_at' => '2017-01-01 11:00:00'],
                    ['id' => $this->number(5), 'updated_at' => '2017-01-01 10:00:00'],
                ],
                'hasPrevious' => null,
                'previousCursor' => null,
                'hasNext' => true,
                'nextCursor' => ['updated_at' => '2017-01-01 10:00:00', 'id' => $this->number(5)],
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

    #[Test]
    public function testDescendingForwardInclusive(): void
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => $this->number(3), 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => $this->number(1), 'updated_at' => '2017-01-01 10:00:00'],
                ],
                'hasPrevious' => true,
                'previousCursor' => ['updated_at' => '2017-01-01 10:00:00', 'id' => $this->number(5)],
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

    #[Test]
    public function testDescendingForwardExclusive(): void
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => $this->number(1), 'updated_at' => '2017-01-01 10:00:00'],
                ],
                'hasPrevious' => true,
                'previousCursor' => ['updated_at' => '2017-01-01 10:00:00', 'id' => $this->number(1)],
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

    #[Test]
    public function testDescendingBackwardStartInclusive(): void
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => $this->number(5), 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => $this->number(3), 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => $this->number(1), 'updated_at' => '2017-01-01 10:00:00'],
                ],
                'hasPrevious' => true,
                'previousCursor' => ['updated_at' => '2017-01-01 11:00:00', 'id' => $this->number(2)],
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

    #[Test]
    public function testDescendingBackwardStartExclusive(): void
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => $this->number(5), 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => $this->number(3), 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => $this->number(1), 'updated_at' => '2017-01-01 10:00:00'],
                ],
                'hasPrevious' => true,
                'previousCursor' => ['updated_at' => '2017-01-01 10:00:00', 'id' => $this->number(5)],
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

    #[Test]
    public function testDescendingBackwardInclusive(): void
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => $this->number(2), 'updated_at' => '2017-01-01 11:00:00'],
                    ['id' => $this->number(5), 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => $this->number(3), 'updated_at' => '2017-01-01 10:00:00'],
                ],
                'hasPrevious' => true,
                'previousCursor' => ['updated_at' => '2017-01-01 11:00:00', 'id' => $this->number(4)],
                'hasNext' => true,
                'nextCursor' => ['updated_at' => '2017-01-01 10:00:00', 'id' => $this->number(1)],
            ],
            (new StubPaginator('posts'))
                ->backward()->limit(3)
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->seekable()
                ->paginate(['id' => '3', 'updated_at' => '2017-01-01 10:00:00'])
        );
    }

    #[Test]
    public function testDescendingBackwardExclusive(): void
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => $this->number(4), 'updated_at' => '2017-01-01 11:00:00'],
                    ['id' => $this->number(2), 'updated_at' => '2017-01-01 11:00:00'],
                    ['id' => $this->number(5), 'updated_at' => '2017-01-01 10:00:00'],
                ],
                'hasPrevious' => false,
                'previousCursor' => null,
                'hasNext' => true,
                'nextCursor' => ['updated_at' => '2017-01-01 10:00:00', 'id' => $this->number(5)],
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
