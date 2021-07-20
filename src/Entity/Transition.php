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

use App\Dictionary\EventType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * State transition.
 *
 * @ORM\Entity
 * @ORM\Table(name="transitions")
 */
class Transition
{
    /**
     * Unique ID.
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected int $id;

    /**
     * Event of the transition.
     *
     * @ORM\OneToOne(targetEntity=Event::class)
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id", unique=true, nullable=false, onDelete="CASCADE")
     */
    protected Event $event;

    /**
     * New state.
     *
     * @ORM\ManyToOne(targetEntity=State::class)
     * @ORM\JoinColumn(name="state_id", referencedColumnName="id", nullable=false)
     */
    protected State $state;

    /**
     * List of field values.
     *
     * @ORM\OneToMany(targetEntity=FieldValue::class, mappedBy="transition")
     * @ORM\OrderBy({"field.position": "ASC"})
     */
    protected Collection $values;

    /**
     * Creates new transition.
     */
    public function __construct(Event $event, State $state)
    {
        $supported = [
            EventType::ISSUE_CLOSED,
            EventType::ISSUE_CREATED,
            EventType::ISSUE_REOPENED,
            EventType::STATE_CHANGED,
        ];

        if (!in_array($event->getType(), $supported, true)) {
            throw new \UnexpectedValueException('Invalid event: ' . $event->getType());
        }

        if ($event->getIssue()->getTemplate() !== $state->getTemplate()) {
            throw new \UnexpectedValueException('Unknown state: ' . $state->getName());
        }

        $this->event = $event;
        $this->state = $state;

        $this->values = new ArrayCollection();
    }

    /**
     * Property getter.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Property getter.
     */
    public function getEvent(): Event
    {
        return $this->event;
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
     *
     * @return Collection|FieldValue[]
     */
    public function getValues(): Collection
    {
        return $this->values;
    }
}
