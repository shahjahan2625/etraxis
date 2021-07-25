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

namespace App\Repository\Contracts;

use App\Entity\User;
use Doctrine\Common\Collections\Selectable;
use Doctrine\Persistence\ObjectRepository;

/**
 * Interface to the 'User' entities repository.
 */
interface UserRepositoryInterface extends ObjectRepository, Selectable
{
    /**
     * @see \Doctrine\Persistence\ObjectManager::persist()
     */
    public function persist(User $entity): void;

    /**
     * @see \Doctrine\Persistence\ObjectManager::remove()
     */
    public function remove(User $entity): void;

    /**
     * @see \Doctrine\Persistence\ObjectManager::refresh()
     */
    public function refresh(User $entity): void;

    /**
     * Finds user by email.
     */
    public function findOneByEmail(string $email): ?User;

    /**
     * Finds user by account provider and its UID.
     */
    public function findOneByProviderUid(string $provider, string $uid): ?User;
}
