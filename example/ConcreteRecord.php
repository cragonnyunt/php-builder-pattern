<?php

namespace App;

use PHPBuilder\Contracts\RecordInterface;
use PHPBuilder\Contracts\BuilderInterface;

class ConcreteRecord implements RecordInterface
{
    private $memberCount;

    /**
     * Build the object from builder
     *
     * @param ConcreteBuilder $builder
     */
    public function __construct(BuilderInterface $builder)
    {
        $members = $builder->getMembers();
        $this->memberCount = sizeof($members);
    }

    /**
     * Getter
     *
     * @return int
     */
    public function getMemberCount(): int
    {
        return $this->memberCount;
    }
}
