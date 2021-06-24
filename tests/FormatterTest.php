<?php

namespace Lampager\Tests;

use Lampager\ArrayProcessor;
use Lampager\PaginationResult;
use Lampager\Query;

class FormatterTest extends TestCase
{
    /**
     * @test
     */
    public function defaultFormatter()
    {
        $result = (new StubPaginator('posts'))->orderBy('id')->useProcessor(CustomStubProcessor::class)->paginate();
        $this->assertSame(['This is dummy'], $result);
    }

    /**
     * @test
     */
    public function testStaticCustomFormatter()
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

    /**
     * @test
     */
    public function testInstanceCustomFormatter()
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

    /**
     * @test
     */
    public function testFormatBehavesPriorToInvoke()
    {
        $paginator = new StubPaginator('posts');
        try {
            $result = $paginator->orderBy('id')->useFormatter(CustomStubFormatter::class)->paginate();
            $this->assertSame('Lampager\Tests\CustomStubFormatter::format', $result->called_method);
        } finally {
            $paginator->restoreFormatter();
        }
    }

    /**
     * @test
     */
    public function invalidFormatter()
    {
        $this->expectException(\Lampager\Exceptions\InvalidArgumentException::class);

        (new StubPaginator('posts'))->useProcessor(function () {});
    }

    /**
     * @test
     */
    public function invalidProcessor()
    {
        $this->expectException(\Lampager\Exceptions\InvalidArgumentException::class);

        (new StubPaginator('posts'))->useFormatter(__CLASS__);
    }
}
