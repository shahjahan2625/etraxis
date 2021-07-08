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

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * 'User' entities repository.
 */
class UserRepository extends ServiceEntityRepository implements Contracts\UserRepositoryInterface
{
    /**
     * @codeCoverageIgnore Dependency Injection constructor
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @codeCoverageIgnore Proxy method
     */
    public function persist(User $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }

    /**
     * @codeCoverageIgnore Proxy method
     */
    public function remove(User $entity): void
    {
        $this->getEntityManager()->remove($entity);
    }

    /**
     * @codeCoverageIgnore Proxy method
     */
    public function refresh(User $entity): void
    {
        $this->getEntityManager()->refresh($entity);
    }
}
