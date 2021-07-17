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
use App\Repository\FieldRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as Assert;

/**
 * Field.
 *
 * @ORM\Entity(repositoryClass=FieldRepository::class)
 * @ORM\Table(
 *     name="fields",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(columns={"state_id", "name", "removed_at"}),
 *         @ORM\UniqueConstraint(columns={"state_id", "position", "removed_at"})
 *     }
 * )
 * @Assert\UniqueEntity(fields={"state", "name", "removedAt"}, message="field.conflict.name", ignoreNull=false)
 */
class Field
{
    // Constraints.
    public const MAX_NAME        = 50;
    public const MAX_DESCRIPTION = 1000;

    /**
     * Unique ID.
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected int $id;

    /**
     * State of the field.
     *
     * @ORM\ManyToOne(targetEntity=State::class, inversedBy="fields")
     * @ORM\JoinColumn(name="state_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected State $state;

    /**
     * Name of the field.
     *
     * @ORM\Column(name="name", type="string", length=50)
     */
    protected string $name;

    /**
     * Type of the field (see the "FieldType" dictionary).
     *
     * @ORM\Column(name="type", type="string", length=10)
     */
    protected string $type;

    /**
     * Optional description of the field.
     *
     * @ORM\Column(name="description", type="string", length=1000, nullable=true)
     */
    protected ?string $description = null;

    /**
     * Ordinal number of the field.
     * No duplicates of this number among fields of the same state are allowed.
     *
     * @ORM\Column(name="position", type="integer")
     */
    protected int $position;

    /**
     * Whether the field is required.
     *
     * @ORM\Column(name="required", type="boolean")
     */
    protected bool $isRequired;

    /**
     * Unix Epoch timestamp when the field was removed (soft-deleted).
     *
     * @ORM\Column(name="removed_at", type="integer", nullable=true)
     */
    protected ?int $removedAt = null;

    /**
     * List of field role permissions.
     *
     * @ORM\OneToMany(targetEntity=FieldRolePermission::class, mappedBy="field", orphanRemoval=true)
     */
    protected Collection $rolePermissions;

    /**
     * List of field group permissions.
     *
     * @ORM\OneToMany(targetEntity=FieldGroupPermission::class, mappedBy="field", orphanRemoval=true)
     */
    protected Collection $groupPermissions;

    /**
     * Creates new field for the specified state.
     */
    public function __construct(State $state, string $type)
    {
        if (!FieldType::has($type)) {
            throw new \UnexpectedValueException('Unknown field type: ' . $type);
        }

        $this->state = $state;
        $this->type  = $type;

        $this->rolePermissions  = new ArrayCollection();
        $this->groupPermissions = new ArrayCollection();
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
    public function getState(): State
    {
        return $this->state;
    }

    /**
     * Property getter.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Property setter.
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Property getter.
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Property getter.
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Property setter.
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Property getter.
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * Property setter.
     */
    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Property getter.
     */
    public function isRequired(): bool
    {
        return $this->isRequired;
    }

    /**
     * Property setter.
     */
    public function setRequired(bool $isRequired): self
    {
        $this->isRequired = $isRequired;

        return $this;
    }

    /**
     * Whether the field is removed (soft-deleted).
     */
    public function isRemoved(): bool
    {
        return $this->removedAt !== null;
    }

    /**
     * Marks field as removed (soft-deleted).
     */
    public function remove(): self
    {
        if ($this->removedAt === null) {
            $this->removedAt = time();
        }

        return $this;
    }

    /**
     * Property getter.
     *
     * @return Collection|FieldRolePermission[]
     */
    public function getRolePermissions(): Collection
    {
        return $this->rolePermissions;
    }

    /**
     * Property getter.
     *
     * @return Collection|FieldGroupPermission[]
     */
    public function getGroupPermissions(): Collection
    {
        return $this->groupPermissions;
    }
}
