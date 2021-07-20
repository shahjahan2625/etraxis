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

use App\Entity\File;
use Doctrine\Common\Collections\Selectable;
use Doctrine\Persistence\ObjectRepository;

/**
 * Interface to the 'File' entities repository.
 */
interface FileRepositoryInterface extends ObjectRepository, Selectable
{
    /**
     * @see \Doctrine\Persistence\ObjectManager::persist()
     */
    public function persist(File $entity): void;

    /**
     * @see \Doctrine\Persistence\ObjectManager::remove()
     */
    public function remove(File $entity): void;

    /**
     * @see \Doctrine\Persistence\ObjectManager::refresh()
     */
    public function refresh(File $entity): void;
}
