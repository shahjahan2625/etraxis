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

use App\Dictionary\FieldType;
use App\Repository\ListItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as Assert;

/**
 * List item.
 *
 * @ORM\Entity(repositoryClass=ListItemRepository::class)
 * @ORM\Table(
 *     name="list_items",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(columns={"field_id", "item_value"}),
 *         @ORM\UniqueConstraint(columns={"field_id", "item_text"})
 *     }
 * )
 * @Assert\UniqueEntity(fields={"field", "value"}, message="listitem.conflict.value")
 * @Assert\UniqueEntity(fields={"field", "text"}, message="listitem.conflict.text")
 */
class ListItem
{
    // Constraints.
    public const MAX_TEXT = 50;

    /**
     * Unique ID.
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected int $id;

    /**
     * Field.
     *
     * @ORM\ManyToOne(targetEntity=Field::class)
     * @ORM\JoinColumn(name="field_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected Field $field;

    /**
     * Value of the item.
     *
     * @ORM\Column(name="item_value", type="integer")
     */
    protected int $value;

    /**
     * Text of the item.
     *
     * @ORM\Column(name="item_text", type="string", length=50)
     */
    protected string $text;

    /**
     * Adds new item to specified field of "List" type.
     */
    public function __construct(Field $field)
    {
        if ($field->getType() !== FieldType::LIST) {
            throw new \UnexpectedValueException('Invalid field type: ' . $field->getType());
        }

        $this->field = $field;
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
    public function getField(): Field
    {
        return $this->field;
    }

    /**
     * Property getter.
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * Property setter.
     */
    public function setValue(int $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Property getter.
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * Property setter.
     */
    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }
}
