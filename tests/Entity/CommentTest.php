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
use App\Dictionary\StateType;
use App\ReflectionTrait;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversDefaultClass \App\Entity\Comment
 */
final class CommentTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @covers ::__construct
     */
    public function testConstructor(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $user  = new User();
        $issue = new Issue($state, $user);

        $event   = new Event(EventType::PUBLIC_COMMENT, $issue, $user);
        $comment = new Comment($event);

        self::assertSame($event, $comment->getEvent());
        self::assertFalse($comment->isPrivate());

        $event   = new Event(EventType::PRIVATE_COMMENT, $issue, $user);
        $comment = new Comment($event);

        self::assertSame($event, $comment->getEvent());
        self::assertTrue($comment->isPrivate());
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorException(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Invalid event: issue.edited');

        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $user  = new User();
        $issue = new Issue($state, $user);
        $event = new Event(EventType::ISSUE_EDITED, $issue, $user);

        new Comment($event);
    }

    /**
     * @covers ::getId
     */
    public function testId(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $user  = new User();
        $issue = new Issue($state, $user);
        $event = new Event(EventType::PUBLIC_COMMENT, $issue, $user);

        $comment = new Comment($event);

        $this->setProperty($comment, 'id', 1);
        self::assertSame(1, $comment->getId());
    }

    /**
     * @covers ::getEvent
     */
    public function testEvent(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $user  = new User();
        $issue = new Issue($state, $user);
        $event = new Event(EventType::PUBLIC_COMMENT, $issue, $user);

        $comment = new Comment($event);
        self::assertSame($event, $comment->getEvent());
    }

    /**
     * @covers ::getBody
     * @covers ::setBody
     */
    public function testBody(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $user  = new User();
        $issue = new Issue($state, $user);
        $event = new Event(EventType::PUBLIC_COMMENT, $issue, $user);

        $comment = new Comment($event);

        $comment->setBody('Lorem Ipsum');
        self::assertSame('Lorem Ipsum', $comment->getBody());
    }

    /**
     * @covers ::isPrivate
     * @covers ::setPrivate
     */
    public function testPrivate(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $user  = new User();
        $issue = new Issue($state, $user);
        $event = new Event(EventType::PUBLIC_COMMENT, $issue, $user);

        $comment = new Comment($event);
        self::assertFalse($comment->isPrivate());

        $comment->setPrivate(true);
        self::assertTrue($comment->isPrivate());

        $comment->setPrivate(false);
        self::assertFalse($comment->isPrivate());
    }
}
