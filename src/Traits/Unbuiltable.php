<?php

namespace PHPBuilder\Traits;

use PHPBuilder\Contracts\RecordInterface;
use PHPBuilder\Exceptions\ForbiddenBuildException;

/**
 * Indicate that the builder is not intended to be built
 */
trait Unbuiltable
{
    protected function getObject(): RecordInterface
    {
        throw new ForbiddenBuildException();
    }

    /**
     * @throws Exception
     */
    protected function checkPrerequisites(): void
    {
        return;
    }
}
