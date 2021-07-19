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

use App\Dictionary\StateType;
use App\Repository\IssueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Issue.
 *
 * @ORM\Entity(repositoryClass=IssueRepository::class)
 * @ORM\Table(
 *     name="issues",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(columns={"author_id", "created_at"})
 *     }
 * )
 */
class Issue
{
    // Constraints.
    public const MAX_SUBJECT = 250;

    // Number of seconds in one day.
    protected const ONE_DAY = 86400;

    /**
     * Unique ID.
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected int $id;

    /**
     * Subject of the issue.
     *
     * @ORM\Column(name="subject", type="string", length=250)
     */
    protected string $subject;

    /**
     * Current state.
     *
     * @ORM\ManyToOne(targetEntity=State::class)
     * @ORM\JoinColumn(name="state_id", referencedColumnName="id", nullable=false)
     */
    protected State $state;

    /**
     * Author of the issue.
     *
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id", nullable=false)
     */
    protected User $author;

    /**
     * Current responsible of the issue.
     *
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(name="responsible_id", referencedColumnName="id")
     */
    protected ?User $responsible = null;

    /**
     * Original issue this issue was cloned from (when applicable).
     *
     * @ORM\ManyToOne(targetEntity=Issue::class)
     * @ORM\JoinColumn(name="origin_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected ?Issue $origin = null;

    /**
     * Unix Epoch timestamp when the issue has been created.
     *
     * @ORM\Column(name="created_at", type="integer")
     */
    protected int $createdAt;

    /**
     * Unix Epoch timestamp when the issue has been changed last time.
     *
     * @ORM\Column(name="changed_at", type="integer")
     */
    protected int $changedAt;

    /**
     * Unix Epoch timestamp when the issue has been closed, if so.
     *
     * @ORM\Column(name="closed_at", type="integer", nullable=true)
     */
    protected ?int $closedAt = null;

    /**
     * Unix Epoch timestamp when the issue will be resumed, if suspended.
     *
     * @ORM\Column(name="resumes_at", type="integer", nullable=true)
     */
    protected ?int $resumesAt = null;

    /**
     * List of issue events.
     *
     * @ORM\OneToMany(targetEntity=Event::class, mappedBy="issue", orphanRemoval=true)
     * @ORM\OrderBy({"createdAt": "ASC", "id": "ASC"})
     */
    protected Collection $events;

    /**
     * Creates new issue.
     */
    public function __construct(State $state, User $author, ?self $origin = null)
    {
        if ($origin !== null && $origin->getTemplate() !== $state->getTemplate()) {
            throw new \UnexpectedValueException('Invalid origin: ' . $origin->getFullId());
        }

        $this->state  = $state;
        $this->author = $author;
        $this->origin = $origin;

        $this->createdAt = $this->changedAt = time();

        if ($state->isFinal()) {
            $this->closedAt = $this->createdAt;
        }

        $this->events = new ArrayCollection();
    }

    /**
     * Property getter.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Returns full unique ID with template prefix.
     */
    public function getFullId(): string
    {
        return sprintf('%s-%03d', $this->state->getTemplate()->getPrefix(), $this->id);
    }

    /**
     * Property getter.
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * Property setter.
     */
    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Property getter.
     */
    public function getProject(): Project
    {
        return $this->state->getTemplate()->getProject();
    }

    /**
     * Property getter.
     */
    public function getTemplate(): Template
    {
        return $this->state->getTemplate();
    }

    /**
     * Property getter.
     */
    public function getState(): State
    {
        return $this->state;
    }

    /**
     * Property setter.
     */
    public function setState(State $state): self
    {
        if ($this->state->getTemplate() === $state->getTemplate()) {
            $this->state    = $state;
            $this->closedAt = $state->getType() === StateType::FINAL ? time() : null;
        }
        else {
            throw new \UnexpectedValueException('Unknown state: ' . $state->getName());
        }

        return $this;
    }

    /**
     * Property getter.
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * Property getter.
     */
    public function getResponsible(): ?User
    {
        return $this->responsible;
    }

    /**
     * Property setter.
     */
    public function setResponsible(?User $responsible): self
    {
        $this->responsible = $responsible;

        return $this;
    }

    /**
     * Property getter.
     */
    public function getOrigin(): ?self
    {
        return $this->origin;
    }

    /**
     * Whether the issue was cloned.
     */
    public function isCloned(): bool
    {
        return $this->origin !== null;
    }

    /**
     * Property getter.
     */
    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    /**
     * Returns number of days the issue remained or remains opened.
     */
    public function getAge(): int
    {
        return (int) ceil((($this->closedAt ?? time()) - $this->createdAt) / self::ONE_DAY);
    }

    /**
     * Property getter.
     */
    public function getChangedAt(): int
    {
        return $this->changedAt;
    }

    /**
     * Updates the timestamp of when the issue has been changed.
     */
    public function touch(): void
    {
        $this->changedAt = time();
    }

    /**
     * Property getter.
     */
    public function getClosedAt(): ?int
    {
        return $this->closedAt;
    }

    /**
     * Whether the issue is closed.
     */
    public function isClosed(): bool
    {
        return $this->closedAt !== null;
    }

    /**
     * Whether the issue is critical (remains opened for too long).
     */
    public function isCritical(): bool
    {
        return !$this->isClosed()
            && $this->state->getTemplate()->getCriticalAge() !== null
            && $this->state->getTemplate()->getCriticalAge() < $this->getAge();
    }

    /**
     * Whether the issue is frozen (read-only).
     */
    public function isFrozen(): bool
    {
        return $this->isClosed()
            && $this->state->getTemplate()->getFrozenTime() !== null
            && $this->state->getTemplate()->getFrozenTime() < ceil((time() - $this->closedAt) / self::ONE_DAY);
    }

    /**
     * Property getter.
     */
    public function getResumesAt(): ?int
    {
        return $this->resumesAt;
    }

    /**
     * Whether the issue is currently suspended.
     */
    public function isSuspended(): bool
    {
        return $this->resumesAt !== null && $this->resumesAt > time();
    }

    /**
     * Suspends the issue until specified timestamp.
     *
     * @param int $timestamp Unix Epoch timestamp
     */
    public function suspend(int $timestamp): void
    {
        $this->resumesAt = $timestamp;
    }

    /**
     * Resumes the issue if suspended.
     */
    public function resume(): void
    {
        $this->resumesAt = null;
    }

    /**
     * Property getter.
     *
     * @return Collection|Event[]
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }
}
