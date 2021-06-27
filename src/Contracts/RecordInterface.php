<?php

namespace PHPBuilder\Contracts;

/**
 * Interface for building result of builder
 */
interface RecordInterface
{
    public function __construct(BuilderInterface $builder);
}
