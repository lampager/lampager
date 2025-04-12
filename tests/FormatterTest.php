<?php

namespace Lampager\Tests;

use Lampager\ArrayProcessor;
use Lampager\Exceptions\InvalidArgumentException;
use Lampager\PaginationResult;
use Lampager\Query;
use PHPUnit\Framework\Attributes\Test;

class FormatterTest extends TestCase
{
    #[Test]
    public function defaultFormatter(): void
    {
        $result = (new StubPaginator('posts'))->orderBy('id')->useProcessor(CustomStubProcessor::class)->paginate();
        $this->assertSame(['This is dummy'], $result);
    }

    #[Test]
    public function testStaticCustomFormatter(): void
    {
        try {
            ArrayProcessor::setDefaultFormatter(function ($rows, $meta, Query $query) {
                $this->assertSame('posts', $query->builder());
                return new PaginationResult($rows, array_replace($meta, ['foo' => 'bar']));
            });
            $result = (new StubPaginator('posts'))->orderBy('id')->paginate();
            $this->assertSame('bar', $result->foo);
        } finally {
            ArrayProcessor::restoreDefaultFormatter();
        }
    }

    #[Test]
    public function testInstanceCustomFormatter(): void
    {
        $paginator = new StubPaginator('posts');
        try {
            $result = $paginator->orderBy('id')->useFormatter(function ($rows, $meta, Query $query) {
                $this->assertSame('posts', $query->builder());
                return new PaginationResult($rows, array_replace($meta, ['foo' => 'bar']));
            })->paginate();
            $this->assertSame('bar', $result->foo);
        } finally {
            $paginator->restoreFormatter();
        }
    }

    #[Test]
    public function testFormatBehavesPriorToInvoke(): void
    {
        $paginator = new StubPaginator('posts');
        try {
            $result = $paginator->orderBy('id')->useFormatter(CustomStubFormatter::class)->paginate();
            $this->assertSame('Lampager\Tests\CustomStubFormatter::format', $result->called_method);
        } finally {
            $paginator->restoreFormatter();
        }
    }

    #[Test]
    public function invalidFormatter(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new StubPaginator('posts'))->useProcessor(function () {});
    }

    #[Test]
    public function invalidProcessor(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new StubPaginator('posts'))->useFormatter(__CLASS__);
    }
}
