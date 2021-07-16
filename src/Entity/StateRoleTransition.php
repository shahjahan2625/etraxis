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

use App\Dictionary\SystemRole;
use Doctrine\ORM\Mapping as ORM;

/**
 * State transition for system role.
 *
 * @ORM\Entity
 * @ORM\Table(name="state_role_transitions")
 */
class StateRoleTransition
{
    /**
     * State the transition goes from.
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=State::class, inversedBy="roleTransitions")
     * @ORM\JoinColumn(name="state_from_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected State $fromState;

    /**
     * State the transition goes to.
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=State::class)
     * @ORM\JoinColumn(name="state_to_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected State $toState;

    /**
     * System role.
     *
     * @ORM\Id
     * @ORM\Column(name="role", type="string", length=20)
     */
    protected string $role;

    /**
     * Constructor.
     */
    public function __construct(State $fromState, State $toState, string $role)
    {
        if ($fromState->getTemplate() !== $toState->getTemplate()) {
            throw new \UnexpectedValueException('States must belong the same template.');
        }

        if (!SystemRole::has($role)) {
            throw new \UnexpectedValueException('Unknown system role: ' . $role);
        }

        $this->fromState = $fromState;
        $this->toState   = $toState;
        $this->role      = $role;
    }

    /**
     * Property getter.
     */
    public function getFromState(): State
    {
        return $this->fromState;
    }

    /**
     * Property getter.
     */
    public function getToState(): State
    {
        return $this->toState;
    }

    /**
     * Property getter.
     */
    public function getRole(): string
    {
        return $this->role;
    }
}
