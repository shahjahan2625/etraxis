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

use App\Dictionary\AccountProvider;
use App\Dictionary\Locale;
use App\Dictionary\Timezone;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

/**
 * User.
 *
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(
 *     name="users",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(columns={"account_provider", "account_uid"})
 *     }
 * )
 * @Assert\UniqueEntity(fields={"email"}, message="user.conflict.email")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    // Roles.
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_USER  = 'ROLE_USER';

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
     * Whether the user has administration privileges.
     *
     * @ORM\Column(name="admin", type="boolean")
     */
    protected bool $isAdmin;

    /**
     * Account provider (see the "AccountProvider" dictionary).
     *
     * @ORM\Column(name="account_provider", type="string", length=20)
     */
    protected string $accountProvider;

    /**
     * Account UID as in the external provider's system.
     *
     * @ORM\Column(name="account_uid", type="string")
     */
    protected string $accountUid;

    /**
     * User's settings.
     *
     * @ORM\Column(name="settings", type="json", nullable=true)
     */
    protected ?array $settings = null;

    /**
     * List of groups the user is member of.
     *
     * @ORM\ManyToMany(targetEntity=Group::class, mappedBy="members")
     * @ORM\OrderBy({"name": "ASC", "project": "ASC"})
     */
    protected Collection $groups;

    /**
     * Creates new user.
     */
    public function __construct()
    {
        $this->isAdmin = false;

        $this->accountProvider = AccountProvider::ETRAXIS;
        $this->accountUid      = Uuid::v4()->toRfc4122();

        $this->groups = new ArrayCollection();
    }

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
        return [$this->isAdmin ? self::ROLE_ADMIN : self::ROLE_USER];
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
    public function getId(): int
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

    /**
     * Property getter.
     */
    public function isAdmin(): bool
    {
        return $this->isAdmin;
    }

    /**
     * Property setter.
     */
    public function setAdmin(bool $isAdmin): self
    {
        $this->isAdmin = $isAdmin;

        return $this;
    }

    /**
     * Property getter.
     */
    public function getAccountProvider(): string
    {
        return $this->accountProvider;
    }

    /**
     * Property setter.
     */
    public function setAccountProvider(string $provider): self
    {
        if (!AccountProvider::has($provider)) {
            throw new \UnexpectedValueException('Unknown account provider: ' . $provider);
        }

        $this->accountProvider = $provider;

        return $this;
    }

    /**
     * Property getter.
     */
    public function getAccountUid(): string
    {
        return $this->accountUid;
    }

    /**
     * Property setter.
     */
    public function setAccountUid(string $uid): self
    {
        $this->accountUid = $uid;

        return $this;
    }

    /**
     * Property getter.
     */
    public function getLocale(): string
    {
        return $this->settings['locale'] ?? Locale::FALLBACK;
    }

    /**
     * Property setter.
     */
    public function setLocale(string $locale): self
    {
        if (Locale::has($locale)) {
            $this->settings['locale'] = $locale;
        }

        return $this;
    }

    /**
     * Property getter.
     */
    public function getTimezone(): string
    {
        return $this->settings['timezone'] ?? Timezone::FALLBACK;
    }

    /**
     * Property setter.
     */
    public function setTimezone(string $timezone): self
    {
        if (Timezone::has($timezone)) {
            $this->settings['timezone'] = $timezone;
        }

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

    /**
     * Checks whether the account is loaded from a 3rd party provider.
     */
    public function isAccountExternal(): bool
    {
        return $this->accountProvider !== AccountProvider::ETRAXIS;
    }
}
