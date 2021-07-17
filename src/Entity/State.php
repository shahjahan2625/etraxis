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

use App\Dictionary\StateResponsible;
use App\Dictionary\StateType;
use App\Repository\StateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as Assert;

/**
 * State.
 *
 * @ORM\Entity(repositoryClass=StateRepository::class)
 * @ORM\Table(
 *     name="states",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(columns={"template_id", "name"})
 *     }
 * )
 * @Assert\UniqueEntity(fields={"template", "name"}, message="state.conflict.name")
 */
class State
{
    // Constraints.
    public const MAX_NAME = 50;

    /**
     * Unique ID.
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected int $id;

    /**
     * Template of the state.
     *
     * @ORM\ManyToOne(targetEntity=Template::class, inversedBy="states")
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected Template $template;

    /**
     * Name of the state.
     *
     * @ORM\Column(name="name", type="string", length=50)
     */
    protected string $name;

    /**
     * Type of the state (see the "StateType" dictionary).
     *
     * @ORM\Column(name="type", type="string", length=12)
     */
    protected string $type;

    /**
     * Type of responsibility management (see the "StateResponsible" dictionary).
     *
     * @ORM\Column(name="responsible", type="string", length=10)
     */
    protected string $responsible;

    /**
     * Next state by default.
     *
     * @ORM\ManyToOne(targetEntity=State::class)
     * @ORM\JoinColumn(name="next_state_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected ?State $nextState = null;

    /**
     * List of state fields.
     *
     * @ORM\OneToMany(targetEntity=Field::class, mappedBy="state", orphanRemoval=true)
     * @ORM\OrderBy({"position": "ASC"})
     */
    protected Collection $fields;

    /**
     * List of state role transitions.
     *
     * @ORM\OneToMany(targetEntity=StateRoleTransition::class, mappedBy="fromState", orphanRemoval=true)
     */
    protected Collection $roleTransitions;

    /**
     * List of state group transitions.
     *
     * @ORM\OneToMany(targetEntity=StateGroupTransition::class, mappedBy="fromState", orphanRemoval=true)
     */
    protected Collection $groupTransitions;

    /**
     * List of responsible groups.
     *
     * @ORM\OneToMany(targetEntity=StateResponsibleGroup::class, mappedBy="state", orphanRemoval=true)
     */
    protected Collection $responsibleGroups;

    /**
     * Creates new state in the specified template.
     */
    public function __construct(Template $template, string $type)
    {
        if (!StateType::has($type)) {
            throw new \UnexpectedValueException('Unknown state type: ' . $type);
        }

        $this->template    = $template;
        $this->type        = $type;
        $this->responsible = StateResponsible::REMOVE;

        $this->fields            = new ArrayCollection();
        $this->roleTransitions   = new ArrayCollection();
        $this->groupTransitions  = new ArrayCollection();
        $this->responsibleGroups = new ArrayCollection();
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
    public function getTemplate(): Template
    {
        return $this->template;
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
    public function getResponsible(): string
    {
        return $this->type === StateType::FINAL
            ? StateResponsible::REMOVE
            : $this->responsible;
    }

    /**
     * Property setter.
     */
    public function setResponsible(string $responsible): self
    {
        if (StateResponsible::has($responsible)) {
            $this->responsible = $responsible;
        }
        else {
            throw new \UnexpectedValueException('Unknown responsibility type: ' . $responsible);
        }

        return $this;
    }

    /**
     * Property getter.
     */
    public function getNextState(): ?self
    {
        return $this->type === StateType::FINAL
            ? null
            : $this->nextState;
    }

    /**
     * Property setter.
     */
    public function setNextState(?self $nextState): self
    {
        if ($nextState === null || $nextState->getTemplate() === $this->getTemplate()) {
            $this->nextState = $nextState;
        }
        else {
            throw new \UnexpectedValueException('Unknown state: ' . $nextState->getName());
        }

        return $this;
    }

    /**
     * Property getter.
     *
     * @return Collection|Field[]
     */
    public function getFields(): Collection
    {
        return $this->fields->filter(fn (Field $field) => !$field->isRemoved());
    }

    /**
     * Property getter.
     *
     * @return Collection|StateRoleTransition[]
     */
    public function getRoleTransitions(): Collection
    {
        return $this->roleTransitions;
    }

    /**
     * Property getter.
     *
     * @return Collection|StateGroupTransition[]
     */
    public function getGroupTransitions(): Collection
    {
        return $this->groupTransitions;
    }

    /**
     * Property getter.
     *
     * @return Collection|StateResponsibleGroup[]
     */
    public function getResponsibleGroups(): Collection
    {
        return $this->responsibleGroups;
    }

    /**
     * Whether the state is final.
     */
    public function isFinal(): bool
    {
        return $this->type === StateType::FINAL;
    }
}
