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

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * State responsible group.
 *
 * @ORM\Entity
 * @ORM\Table(name="state_responsible_groups")
 */
class StateResponsibleGroup
{
    /**
     * State.
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=State::class, inversedBy="responsibleGroups")
     * @ORM\JoinColumn(name="state_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected State $state;

    /**
     * Group.
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=Group::class)
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected Group $group;

    /**
     * Constructor.
     */
    public function __construct(State $state, Group $group)
    {
        if (!$group->isGlobal() && $group->getProject() !== $state->getTemplate()->getProject()) {
            throw new \UnexpectedValueException('Unknown group: ' . $group->getName());
        }

        $this->state = $state;
        $this->group = $group;
    }

    /**
     * Property getter.
     */
    public function getState(): State
    {
        return $this->state;
    }

    /**
     * Property getter.
     */
    public function getGroup(): Group
    {
        return $this->group;
    }
}
