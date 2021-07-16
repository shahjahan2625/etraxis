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

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as Assert;

/**
 * Project.
 *
 * @ORM\Entity(repositoryClass=ProjectRepository::class)
 * @ORM\Table(name="projects")
 * @Assert\UniqueEntity(fields={"name"}, message="project.conflict.name")
 */
class Project
{
    // Constraints.
    public const MAX_NAME        = 25;
    public const MAX_DESCRIPTION = 100;

    /**
     * Unique ID.
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected int $id;

    /**
     * Name of the project.
     *
     * @ORM\Column(name="name", type="string", length=25, unique=true)
     */
    protected string $name;

    /**
     * Optional description of the project.
     *
     * @ORM\Column(name="description", type="string", length=100, nullable=true)
     */
    protected ?string $description = null;

    /**
     * Unix Epoch timestamp when the project has been registered.
     *
     * @ORM\Column(name="created_at", type="integer")
     */
    protected int $createdAt;

    /**
     * Whether the project is suspended.
     * When project is suspended, its issues are read-only, and new issues cannot be created.
     *
     * @ORM\Column(name="suspended", type="boolean")
     */
    protected bool $isSuspended;

    /**
     * List of project groups.
     *
     * @ORM\OneToMany(targetEntity=Group::class, mappedBy="project", orphanRemoval=true)
     * @ORM\OrderBy({"name": "ASC"})
     */
    protected Collection $groups;

    /**
     * Creates new project.
     */
    public function __construct()
    {
        $this->createdAt   = time();
        $this->isSuspended = false;

        $this->groups = new ArrayCollection();
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
    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    /**
     * Property getter.
     */
    public function isSuspended(): bool
    {
        return $this->isSuspended;
    }

    /**
     * Property setter.
     */
    public function setSuspended(bool $isSuspended): self
    {
        $this->isSuspended = $isSuspended;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return Collection|Group[]
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }
}
