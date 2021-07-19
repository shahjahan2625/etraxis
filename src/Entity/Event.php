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

use App\Repository\EventRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Event.
 *
 * @ORM\Entity(repositoryClass=EventRepository::class)
 * @ORM\Table(
 *     name="events",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(columns={"type", "issue_id", "user_id", "created_at"})
 *     }
 * )
 */
class Event
{
    // Constraints.
    public const MAX_PARAMETER = 100;

    /**
     * Unique ID.
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected int $id;

    /**
     * Type of the event (see the "EventType" dictionary).
     *
     * @ORM\Column(name="type", type="string", length=20)
     */
    protected string $type;

    /**
     * Issue of the event.
     *
     * @ORM\ManyToOne(targetEntity=Issue::class, inversedBy="events")
     * @ORM\JoinColumn(name="issue_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected Issue $issue;

    /**
     * Initiator of the event.
     *
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    protected User $user;

    /**
     * Unix Epoch timestamp when the event has happened.
     *
     * @ORM\Column(name="created_at", type="integer")
     */
    protected int $createdAt;

    /**
     * Event parameter.
     *
     * Depends on the event type as following:
     *     ISSUE_CREATED      - Initial state (state name)
     *     ISSUE_EDITED       - NULL (not used)
     *     STATE_CHANGED      - New state (state name)
     *     ISSUE_REOPENED     - New state of the reopened issue (state name)
     *     ISSUE_CLOSED       - New state of the closed issue (state name)
     *     ISSUE_ASSIGNED     - Responsible user (user's full name)
     *     ISSUE_SUSPENDED    - NULL (not used)
     *     ISSUE_RESUMED      - NULL (not used)
     *     PUBLIC_COMMENT     - NULL (not used)
     *     PRIVATE_COMMENT    - NULL (not used)
     *     FILE_ATTACHED      - Attached file (name of the attachment)
     *     FILE_DELETED       - Deleted file (name of the attachment)
     *     DEPENDENCY_ADDED   - Dependency issue (issue reference)
     *     DEPENDENCY_REMOVED - Dependency issue (issue reference)
     *
     * @ORM\Column(name="parameter", type="string", length=100, nullable=true)
     */
    protected ?string $parameter = null;

    /**
     * Creates new event.
     */
    public function __construct(string $type, Issue $issue, User $user, ?string $parameter = null)
    {
        $this->type      = $type;
        $this->issue     = $issue;
        $this->user      = $user;
        $this->createdAt = time();
        $this->parameter = $parameter;
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
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Property getter.
     */
    public function getIssue(): Issue
    {
        return $this->issue;
    }

    /**
     * Property getter.
     */
    public function getUser(): User
    {
        return $this->user;
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
    public function getParameter(): ?string
    {
        return $this->parameter;
    }
}
