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

use App\Entity\Issue;
use Doctrine\Common\Collections\Selectable;
use Doctrine\Persistence\ObjectRepository;

/**
 * Interface to the 'Issue' entities repository.
 */
interface IssueRepositoryInterface extends ObjectRepository, Selectable
{
    /**
     * @see \Doctrine\Persistence\ObjectManager::persist()
     */
    public function persist(Issue $entity): void;

    /**
     * @see \Doctrine\Persistence\ObjectManager::remove()
     */
    public function remove(Issue $entity): void;

    /**
     * @see \Doctrine\Persistence\ObjectManager::refresh()
     */
    public function refresh(Issue $entity): void;
}
