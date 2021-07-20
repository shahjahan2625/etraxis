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
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversDefaultClass \App\Entity\Watcher
 */
final class WatcherTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstructor(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $user  = new User();
        $issue = new Issue($state, $user);

        $watcher = new Watcher($issue, $user);

        self::assertSame($issue, $watcher->getIssue());
        self::assertSame($user, $watcher->getUser());
    }

    /**
     * @covers ::getIssue
     */
    public function testIssue(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $user  = new User();
        $issue = new Issue($state, $user);

        $watcher = new Watcher($issue, $user);
        self::assertSame($issue, $watcher->getIssue());
    }

    /**
     * @covers ::getUser
     */
    public function testUser(): void
    {
        $state = new State(new Template(new Project()), StateType::INTERMEDIATE);
        $user  = new User();
        $issue = new Issue($state, $user);

        $watcher = new Watcher($issue, $user);
        self::assertSame($user, $watcher->getUser());
    }
}
