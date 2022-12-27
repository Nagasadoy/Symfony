<?php

namespace App\Doctrine;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class BookColorFilter extends SQLFilter
{

    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        if ($targetEntity->getReflectionClass()->name != 'App\Entity\Book') {
            return '';
        }
        return sprintf('%s.color = %s', $targetTableAlias, $this->getParameter('color'));
    }
}