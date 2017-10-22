<?php

namespace Lampager\Concerns;

use Lampager\Contracts\Formatter;
use Lampager\AbstractProcessor;
use Lampager\Query;

trait HasProcessor
{
    /**
     * @var AbstractProcessor
     */
    protected $processor;

    /**
     * Use custom formatter.
     *
     * @param  callable|Formatter|string $formatter
     * @return $this
     */
    public function useFormatter($formatter)
    {
        $this->processor->useFormatter($formatter);
        return $this;
    }

    /**
     * Restore default formatter.
     *
     * @return $this
     */
    public function restoreFormatter()
    {
        $this->processor->restoreFormatter();
        return $this;
    }

    /**
     * Get result from external resources.
     *
     * @param  Query $query
     * @param  mixed $rows
     * @return mixed
     */
    public function process(Query $query, $rows)
    {
        return $this->processor->process($query, $rows);
    }

    /**
     * Use custom processor.
     *
     * @param  AbstractProcessor|string $processor
     * @return $this
     */
    public function useProcessor($processor)
    {
        $this->processor = static::validateProcessor($processor);
        return $this;
    }

    /**
     * Validate processor and return in normalized form.
     *
     * @param  mixed             $processor
     * @return AbstractProcessor
     */
    protected static function validateProcessor($processor)
    {
        if (!is_subclass_of($processor, AbstractProcessor::class)) {
            throw new \InvalidArgumentException('Processor must be an instanceof ' . AbstractProcessor::class);
        }
        return is_string($processor) ? new $processor() : $processor;
    }
}
