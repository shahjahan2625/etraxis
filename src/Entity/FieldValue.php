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
 * Field value.
 *
 * @ORM\Entity
 * @ORM\Table(
 *     name="field_values",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(columns={"transition_id", "field_id"})
 *     }
 * )
 */
class FieldValue
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
     * Transition.
     *
     * @ORM\ManyToOne(targetEntity=Transition::class, inversedBy="fieldValues")
     * @ORM\JoinColumn(name="transition_id", referencedColumnName="id", nullable=false)
     */
    protected Transition $transition;

    /**
     * Field.
     *
     * @ORM\ManyToOne(targetEntity=Field::class)
     * @ORM\JoinColumn(name="field_id", referencedColumnName="id", nullable=false)
     */
    protected Field $field;

    /**
     * New value.
     *
     * Depends on the field type as following:
     *     checkbox - state of checkbox (0 - unchecked, 1 - checked)
     *     date     - date value (Unix Epoch timestamp)
     *     decimal  - decimal value (foreign key to "DecimalValue" entity)
     *     duration - duration value (total number of minutes from 0:00 till 999999:59)
     *     issue    - issue ID (foreign key to "Issue" entity)
     *     list     - integer value (foreign key to "ListItem" entity)
     *     number   - integer value (from -1000000000 till +1000000000)
     *     string   - string value (foreign key to "StringValue" entity)
     *     text     - text value (foreign key to "TextValue" entity)
     *
     * @ORM\Column(name="value", type="integer", nullable=true)
     */
    protected ?int $value = null;

    /**
     * Creates new field value.
     */
    public function __construct(Transition $transition, Field $field, ?int $value)
    {
        if ($transition->getState() !== $field->getState()) {
            throw new \UnexpectedValueException('Unknown field: ' . $field->getName());
        }

        $this->transition = $transition;
        $this->field      = $field;
        $this->value      = $value;
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
    public function getTransition(): Transition
    {
        return $this->transition;
    }

    /**
     * Property getter.
     */
    public function getField(): Field
    {
        return $this->field;
    }

    /**
     * Property getter.
     */
    public function getValue(): ?int
    {
        return $this->value;
    }
}
