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

use App\Dictionary\EventType;
use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Issue comment.
 *
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 * @ORM\Table(name="comments")
 */
class Comment
{
    // Constraints.
    public const MAX_VALUE = 10000;

    /**
     * Unique ID.
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected int $id;

    /**
     * Event of the comment.
     *
     * @ORM\OneToOne(targetEntity=Event::class)
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id", unique=true, nullable=false, onDelete="CASCADE")
     */
    protected Event $event;

    /**
     * Comment's body.
     *
     * @ORM\Column(name="body", type="text")
     */
    protected string $body;

    /**
     * Whether the comment is private.
     *
     * @ORM\Column(name="is_private", type="boolean")
     */
    protected bool $isPrivate;

    /**
     * Creates new comment.
     */
    public function __construct(Event $event)
    {
        if (!in_array($event->getType(), [EventType::PUBLIC_COMMENT, EventType::PRIVATE_COMMENT], true)) {
            throw new \UnexpectedValueException('Invalid event: ' . $event->getType());
        }

        $this->event     = $event;
        $this->isPrivate = $event->getType() === EventType::PRIVATE_COMMENT;
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
    public function getEvent(): Event
    {
        return $this->event;
    }

    /**
     * Property getter.
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * Property setter.
     */
    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Property getter.
     */
    public function isPrivate(): bool
    {
        return $this->isPrivate;
    }

    /**
     * Property setter.
     */
    public function setPrivate(bool $isPrivate): self
    {
        $this->isPrivate = $isPrivate;

        return $this;
    }
}
