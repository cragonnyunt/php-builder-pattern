<?php

namespace App;

use PHPBuilder\Builder;
use PHPBuilder\Contracts\RecordInterface;
use PHPBuilder\Exceptions\OmitException;

/**
 * @method self setMembers(array $members)
 * @method array getMembers()
 */
class ConcreteBuilder extends Builder
{
    /**
     * Build the actual object
     *
     * @param ConcreteBuilder $builder
     */
    protected function getObject(): RecordInterface
    {
        return new ConcreteRecord($this);
    }

    protected function checkPrerequisites(): void
    {
        if (empty($this->data['members'] ?? '')) {
            throw new OmitException('Members are not set');
        }
    }
}
