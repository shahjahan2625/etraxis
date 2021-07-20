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
use App\ReflectionTrait;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversDefaultClass \App\Entity\LastRead
 */
final class LastReadTest extends TestCase
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

        $lastRead = new LastRead($issue, $user);

        self::assertSame($issue, $lastRead->getIssue());
        self::assertSame($user, $lastRead->getUser());

        $timestamp = $this->getProperty($lastRead, 'readAt');
        self::assertLessThanOrEqual(2, time() - $timestamp);
    }

    /**
     * @covers ::getIssue
     */
    public function testIssue(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $user  = new User();
        $issue = new Issue($state, $user);

        $lastRead = new LastRead($issue, $user);
        self::assertSame($issue, $lastRead->getIssue());
    }

    /**
     * @covers ::getUser
     */
    public function testUser(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $user  = new User();
        $issue = new Issue($state, $user);

        $lastRead = new LastRead($issue, $user);
        self::assertSame($user, $lastRead->getUser());
    }

    /**
     * @covers ::touch
     */
    public function testReadAt(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $user  = new User();
        $issue = new Issue($state, $user);

        $lastRead = new LastRead($issue, $user);

        $this->setProperty($lastRead, 'readAt', 0);
        $lastRead->touch();

        $timestamp = $this->getProperty($lastRead, 'readAt');
        self::assertLessThanOrEqual(2, time() - $timestamp);
    }
}
