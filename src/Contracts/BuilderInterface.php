<?php

namespace PHPBuilder\Contracts;

/**
 * Interface for builder classes
 */
interface BuilderInterface
{
    public function build(): ?RecordInterface;
}
