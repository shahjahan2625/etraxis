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

use App\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as Assert;

/**
 * Group.
 *
 * @ORM\Entity(repositoryClass=GroupRepository::class)
 * @ORM\Table(name="groups")
 * @Assert\UniqueEntity(fields={"name"}, message="group.conflict.name")
 */
class Group
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
     * Name of the group.
     *
     * @ORM\Column(name="name", type="string", length=25, unique=true)
     */
    protected string $name;

    /**
     * Optional description of the group.
     *
     * @ORM\Column(name="description", type="string", length=100, nullable=true)
     */
    protected ?string $description = null;

    /**
     * List of members.
     *
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="groups")
     * @ORM\JoinTable(
     *     name="membership",
     *     joinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
     * )
     * @ORM\OrderBy({"fullname": "ASC", "email": "ASC"})
     */
    protected Collection $members;

    /**
     * Creates new group.
     */
    public function __construct()
    {
        $this->members = new ArrayCollection();
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
     *
     * @return Collection|User[]
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    /**
     * Adds user to the group.
     */
    public function addMember(User $user): self
    {
        if (!$this->members->contains($user)) {
            $this->members[] = $user;
        }

        return $this;
    }

    /**
     * Removes user from the group.
     */
    public function removeMember(User $user): self
    {
        $this->members->removeElement($user);

        return $this;
    }
}
