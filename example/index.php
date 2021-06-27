<?php

$builder = new App\ConcreteBuilder();
/**
 * @var App\ConcreteRecord
 */
$record = $builder->setMembers([
            'MemberA',
            'MemberB',
            'MemberC'
        ])->build();
echo $record->getMemberCount();
