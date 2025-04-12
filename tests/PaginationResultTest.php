<?php

namespace Lampager\Tests;

use PHPUnit\Framework\Attributes\Test;

class PaginationResultTest extends TestCase
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
    public function testTraversedResultEqualsToRecords(): void
    {
        $result = (new StubPaginator('posts'))
            ->forward()->limit(3)
            ->orderBy('updated_at')
            ->orderBy('id')
            ->seekable()
            ->paginate(['id' => '3', 'updated_at' => '2017-01-01 10:00:00']);

        $this->assertResultSame($result->records, iterator_to_array($result));
        $this->assertResultSame(count($result->records), count($result));
    }
}
