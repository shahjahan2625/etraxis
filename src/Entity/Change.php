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
 * Field value change.
 *
 * @ORM\Entity
 * @ORM\Table(
 *     name="changes",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(columns={"event_id", "field_id"})
 *     }
 * )
 */
class Change
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
     * Event.
     *
     * @ORM\ManyToOne(targetEntity=Event::class)
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected Event $event;

    /**
     * Field.
     *
     * @ORM\ManyToOne(targetEntity=Field::class)
     * @ORM\JoinColumn(name="field_id", referencedColumnName="id")
     */
    protected ?Field $field = null;

    /**
     * Old value (@see FieldValue::$value).
     *
     * @ORM\Column(name="old_value", type="integer", nullable=true)
     */
    protected ?int $oldValue = null;

    /**
     * New value (@see FieldValue::$value).
     *
     * @ORM\Column(name="new_value", type="integer", nullable=true)
     */
    protected ?int $newValue = null;

    /**
     * Creates new change.
     */
    public function __construct(Event $event, ?Field $field, ?int $oldValue, ?int $newValue)
    {
        if ($field !== null && $event->getIssue()->getTemplate() !== $field->getState()->getTemplate()) {
            throw new \UnexpectedValueException('Unknown field: ' . $field->getName());
        }

        $this->event    = $event;
        $this->field    = $field;
        $this->oldValue = $oldValue;
        $this->newValue = $newValue;
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
    public function getField(): ?Field
    {
        return $this->field;
    }

    /**
     * Property getter.
     */
    public function getOldValue(): ?int
    {
        return $this->oldValue;
    }

    /**
     * Property getter.
     */
    public function getNewValue(): ?int
    {
        return $this->newValue;
    }
}
