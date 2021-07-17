<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2018 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis. If not, see <https://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace App\Repository;

use App\Entity\Field;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * 'Field' entities repository.
 */
class FieldRepository extends ServiceEntityRepository implements Contracts\FieldRepositoryInterface
{
    /**
     * @codeCoverageIgnore Dependency Injection constructor
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Field::class);
    }

    /**
     * @codeCoverageIgnore Proxy method
     */
    public function persist(Field $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }

    /**
     * @codeCoverageIgnore Proxy method
     */
    public function remove(Field $entity): void
    {
        $this->getEntityManager()->remove($entity);
    }

    /**
     * @codeCoverageIgnore Proxy method
     */
    public function refresh(Field $entity): void
    {
        $this->getEntityManager()->refresh($entity);
    }
}
