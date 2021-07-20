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
use App\Dictionary\MimeType;
use App\Repository\FileRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * Attached file.
 *
 * @ORM\Entity(repositoryClass=FileRepository::class)
 * @ORM\Table(name="files")
 */
class File
{
    // Constraints.
    public const MAX_NAME = 100;
    public const MAX_TYPE = 255;

    /**
     * Unique ID.
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected int $id;

    /**
     * Event of the file.
     *
     * @ORM\OneToOne(targetEntity=Event::class)
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id", unique=true, nullable=false, onDelete="CASCADE")
     */
    protected Event $event;

    /**
     * Unique UID for storage.
     *
     * @ORM\Column(name="uid", type="string", length=36)
     */
    protected string $uid;

    /**
     * File name.
     *
     * @ORM\Column(name="filename", type="string", length=100)
     */
    protected string $name;

    /**
     * File size.
     *
     * @ORM\Column(name="filesize", type="integer")
     */
    protected int $size;

    /**
     * MIME type (see the "MimeType" dictionary).
     *
     * @ORM\Column(name="mimetype", type="string", length=255)
     */
    protected string $type;

    /**
     * Whether the file is removed (soft-deleted).
     *
     * @ORM\Column(name="removed_at", type="integer", nullable=true)
     */
    protected ?int $removedAt = null;

    /**
     * Creates new file.
     */
    public function __construct(Event $event, string $name, int $size, string $type)
    {
        if ($event->getType() !== EventType::FILE_ATTACHED) {
            throw new \UnexpectedValueException('Invalid event: ' . $event->getType());
        }

        $this->uid = Uuid::v4()->toRfc4122();

        $this->event = $event;
        $this->name  = $name;
        $this->size  = $size;
        $this->type  = MimeType::has($type) ? $type : MimeType::FALLBACK;
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
    public function getUid(): string
    {
        return $this->uid;
    }

    /**
     * Property getter.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Property getter.
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * Property getter.
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Whether the file is removed (soft-deleted).
     */
    public function isRemoved(): bool
    {
        return $this->removedAt !== null;
    }

    /**
     * Marks file as removed (soft-deleted).
     */
    public function remove(): void
    {
        if ($this->removedAt === null) {
            $this->removedAt = time();
        }
    }
}
