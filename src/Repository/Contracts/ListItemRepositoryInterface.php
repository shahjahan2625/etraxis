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

use App\Entity\ListItem;
use Doctrine\Common\Collections\Selectable;
use Doctrine\Persistence\ObjectRepository;

/**
 * Interface to the 'ListItem' entities repository.
 */
interface ListItemRepositoryInterface extends ObjectRepository, Selectable
{
    /**
     * @see \Doctrine\Persistence\ObjectManager::persist()
     */
    public function persist(ListItem $entity): void;

    /**
     * @see \Doctrine\Persistence\ObjectManager::remove()
     */
    public function remove(ListItem $entity): void;

    /**
     * @see \Doctrine\Persistence\ObjectManager::refresh()
     */
    public function refresh(ListItem $entity): void;
}
