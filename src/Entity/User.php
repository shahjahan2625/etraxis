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

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User.
 *
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="users")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    // Roles.
    public const ROLE_USER = 'ROLE_USER';

    // Constraints.
    public const MAX_EMAIL       = 254;
    public const MAX_FULLNAME    = 50;
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
     * Email address.
     *
     * @ORM\Column(name="email", type="string", length=254, unique=true)
     */
    protected string $email;

    /**
     * Password.
     *
     * @ORM\Column(name="password", type="string", nullable=true)
     */
    protected ?string $password = null;

    /**
     * Full name.
     *
     * @ORM\Column(name="fullname", type="string", length=50)
     */
    protected string $fullname;

    /**
     * Optional description of the user.
     *
     * @ORM\Column(name="description", type="string", length=100, nullable=true)
     */
    protected ?string $description = null;

    /**
     * @codeCoverageIgnore Deprecated since Symfony 5.3
     *
     * @todo Remove in Symfony 6.0
     */
    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }

    /**
     * {@inheritDoc}
     */
    public function getUserIdentifier(): string
    {
        return $this->getEmail();
    }

    /**
     * {@inheritDoc}
     */
    public function getRoles(): array
    {
        return [self::ROLE_USER];
    }

    /**
     * @codeCoverageIgnore Deprecated since Symfony 5.3
     *
     * @todo Remove in Symfony 6.0
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @codeCoverageIgnore Empty implementation
     */
    public function eraseCredentials(): void
    {
    }

    /**
     * Property getter.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Property getter.
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Property setter.
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Property getter.
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Property setter.
     */
    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Property getter.
     */
    public function getFullname(): string
    {
        return $this->fullname;
    }

    /**
     * Property setter.
     */
    public function setFullname(string $fullname): self
    {
        $this->fullname = $fullname;

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
}
