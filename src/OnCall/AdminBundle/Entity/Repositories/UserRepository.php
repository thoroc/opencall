<?php

namespace OnCall\AdminBundle\Entity\Repositories;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function qbWithRoles()
    {
        return $this->_em->createQueryBuilder()
                    ->where( 'roles is null' );
    }

    public function getUserWithRoles()
    {
        return $this->qbWithRoles()
                    ->getQuery()
                    ->getResult();
    }
}