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
use App\Repository\DependencyRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Issue dependency.
 *
 * @ORM\Entity(repositoryClass=DependencyRepository::class)
 * @ORM\Table(name="dependencies")
 */
class Dependency
{
    /**
     * Unique ID.
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected int $id;

    /**
     * Event of the dependency.
     *
     * @ORM\OneToOne(targetEntity=Event::class)
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id", unique=true, nullable=false, onDelete="CASCADE")
     */
    protected Event $event;

    /**
     * Dependency issue.
     *
     * @ORM\ManyToOne(targetEntity=Issue::class)
     * @ORM\JoinColumn(name="issue_id", referencedColumnName="id", nullable=false)
     */
    protected Issue $issue;

    /**
     * Creates new dependency.
     */
    public function __construct(Event $event, Issue $issue)
    {
        if ($event->getType() !== EventType::DEPENDENCY_ADDED) {
            throw new \UnexpectedValueException('Invalid event: ' . $event->getType());
        }

        $this->event = $event;
        $this->issue = $issue;
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
    public function getIssue(): Issue
    {
        return $this->issue;
    }
}
