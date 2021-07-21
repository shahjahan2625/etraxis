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

namespace App\DataFixtures;

use App\Dictionary\EventType;
use App\Entity\Dependency;
use App\Entity\Event;
use App\Entity\Transition;
use App\ReflectionTrait;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Test fixtures for 'Event' entity.
 */
class EventFixtures extends Fixture implements FixtureInterface, DependentFixtureInterface
{
    use ReflectionTrait;
    use UsersTrait;

    // Number of seconds in one day.
    protected const ONE_DAY = 86400;

    // Number of seconds in one minute.
    protected const ONE_MINUTE = 60;

    // Data structure.
    protected const EVENT_TYPE      = 0;
    protected const EVENT_USER      = 1;
    protected const EVENT_DAY       = 2;
    protected const EVENT_MIN       = 3;
    protected const EVENT_PARAMETER = 4;

    /**
     * {@inheritDoc}
     */
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            StateFixtures::class,
            IssueFixtures::class,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager): void
    {
        $data = [

            'task:%s:1' => [
                [EventType::ISSUE_CREATED,    $this->manager1,   0, 0,  'new'],
                [EventType::ISSUE_EDITED,     $this->manager1,   0, 5,  null],
                [EventType::FILE_ATTACHED,    $this->manager1,   0, 10, 0],
                [EventType::STATE_CHANGED,    $this->manager1,   0, 25, 'assigned'],
                [EventType::ISSUE_ASSIGNED,   $this->manager1,   0, 25, $this->developer1],
                [EventType::DEPENDENCY_ADDED, $this->manager1,   0, 30, 'task:%s:2'],
                [EventType::PUBLIC_COMMENT,   $this->manager1,   1, 0,  null],
                [EventType::ISSUE_CLOSED,     $this->developer1, 3, 0,  'completed'],
            ],

            'task:%s:2' => [
                [EventType::ISSUE_CREATED,   $this->manager2,   0, 0,   'new'],
                [EventType::STATE_CHANGED,   $this->manager1,   0, 10,  'assigned'],
                [EventType::ISSUE_ASSIGNED,  $this->manager1,   0, 10,  $this->developer3],
                [EventType::FILE_ATTACHED,   $this->manager1,   0, 15,  0],
                [EventType::FILE_ATTACHED,   $this->manager1,   0, 20,  1],
                [EventType::PUBLIC_COMMENT,  $this->manager1,   1, 0,   null],
                [EventType::ISSUE_CLOSED,    $this->developer3, 2, 35,  'completed'],
                [EventType::ISSUE_REOPENED,  $this->manager2,   2, 90,  'new'],
                [EventType::ISSUE_EDITED,    $this->manager2,   2, 91,  null],
                [EventType::STATE_CHANGED,   $this->manager2,   2, 95,  'assigned'],
                [EventType::ISSUE_EDITED,    $this->manager2,   2, 96,  null],
                [EventType::ISSUE_ASSIGNED,  $this->manager2,   2, 95,  $this->developer3],
                [EventType::FILE_DELETED,    $this->manager2,   2, 105, 1],
                [EventType::PRIVATE_COMMENT, $this->manager2,   2, 110, null],
                [EventType::FILE_ATTACHED,   $this->developer3, 3, 60,  2],
                [EventType::PUBLIC_COMMENT,  $this->developer3, 3, 65,  null],
            ],

            'task:%s:3' => [
                [EventType::ISSUE_CREATED,  $this->manager3,   0, 0, 'new'],
                [EventType::STATE_CHANGED,  $this->manager3,   0, 5, 'assigned'],
                [EventType::ISSUE_ASSIGNED, $this->manager3,   0, 5, $this->developer1],
                [EventType::ISSUE_CLOSED,   $this->developer1, 5, 0, 'completed'],
            ],

            'task:%s:4' => [
                [EventType::ISSUE_CREATED,  $this->developer1, 0, 0,   'new'],
                [EventType::ISSUE_CLOSED,   $this->manager2,   0, 135, 'duplicated'],
            ],

            'task:%s:5' => [
                [EventType::ISSUE_CREATED,  $this->manager3, 0, 0, 'new'],
            ],

            'task:%s:6' => [
                [EventType::ISSUE_CREATED,  $this->manager3, 0, 0, 'new'],
            ],

            'task:%s:7' => [
                [EventType::ISSUE_CREATED,  $this->developer2, 0, 0, 'new'],
                [EventType::STATE_CHANGED,  $this->manager2,   1, 0, 'assigned'],
                [EventType::ISSUE_ASSIGNED, $this->manager2,   1, 0, $this->developer2],
                [EventType::ISSUE_CLOSED,   $this->manager3,   2, 0, 'duplicated'],
            ],

            'task:%s:8' => [
                [EventType::ISSUE_CREATED,  $this->developer2, 0, 0, 'new'],
                [EventType::STATE_CHANGED,  $this->manager1,   3, 0, 'assigned'],
                [EventType::ISSUE_ASSIGNED, $this->manager1,   3, 0, $this->developer2],
            ],

            'req:%s:1' => [
                [EventType::ISSUE_CREATED,  $this->client1,  0, 0, 'submitted'],
                [EventType::STATE_CHANGED,  $this->manager1, 0, 5, 'opened'],
                [EventType::ISSUE_ASSIGNED, $this->manager1, 0, 5, $this->support1],
                [EventType::ISSUE_CLOSED,   $this->support1, 2, 0, 'resolved'],
            ],

            'req:%s:2' => [
                [EventType::ISSUE_CREATED,    $this->client2,  0, 0,  'submitted'],
                [EventType::STATE_CHANGED,    $this->support2, 0, 5,  'opened'],
                [EventType::ISSUE_ASSIGNED,   $this->support2, 0, 5,  $this->support2],
                [EventType::DEPENDENCY_ADDED, $this->support2, 0, 10, 'req:%s:3'],
            ],

            'req:%s:3' => [
                [EventType::ISSUE_CREATED,  $this->client2,  0, 0, 'submitted'],
                [EventType::STATE_CHANGED,  $this->support2, 0, 5, 'opened'],
                [EventType::ISSUE_ASSIGNED, $this->support2, 0, 5, $this->support2],
                [EventType::ISSUE_CLOSED,   $this->support2, 2, 0, 'resolved'],
            ],

            'req:%s:4' => [
                [EventType::ISSUE_CREATED,  $this->client3,  0, 0, 'submitted'],
                [EventType::STATE_CHANGED,  $this->manager2, 1, 0, 'opened'],
                [EventType::ISSUE_ASSIGNED, $this->manager2, 1, 0, $this->support1],
            ],

            'req:%s:5' => [
                [EventType::ISSUE_CREATED,    $this->client2,  0, 0, 'submitted'],
                [EventType::STATE_CHANGED,    $this->support3, 0, 5, 'opened'],
                [EventType::ISSUE_ASSIGNED,   $this->support3, 0, 5, $this->support3],
                [EventType::DEPENDENCY_ADDED, $this->support3, 1, 0, 'req:%s:3'],
            ],

            'req:%s:6' => [
                [EventType::ISSUE_CREATED,    $this->client1,  0, 0, 'submitted'],
                [EventType::DEPENDENCY_ADDED, $this->manager2, 0, 5, 'req:%s:1'],
                [EventType::DEPENDENCY_ADDED, $this->manager1, 2, 0, 'task:%s:8'],
            ],
        ];

        foreach (['a', 'b', 'c'] as $pref) {

            foreach ($data as $iref => $events) {

                /** @var \App\Entity\Issue $issue */
                $issue = $this->getReference(sprintf($iref, $pref));
                $manager->refresh($issue);

                foreach ($events as $index => $row) {

                    /** @var \App\Entity\User $user */
                    $user = $this->getReference($row[self::EVENT_USER][$pref]);

                    $timestamp = $issue->getCreatedAt()
                        + $row[self::EVENT_DAY] * self::ONE_DAY
                        + $row[self::EVENT_MIN] * self::ONE_MINUTE
                        + $index;

                    $event = new Event($row[self::EVENT_TYPE], $issue, $user);

                    $this->setProperty($event, 'createdAt', $timestamp);
                    $this->setProperty($issue, 'changedAt', $timestamp);

                    switch ($row[self::EVENT_TYPE]) {

                        case EventType::ISSUE_CREATED:
                        case EventType::ISSUE_REOPENED:
                        case EventType::ISSUE_CLOSED:
                        case EventType::STATE_CHANGED:

                            /** @var \App\Entity\State $state */
                            $state = $this->getReference(sprintf('%s:%s', $row[self::EVENT_PARAMETER], $pref));
                            $this->setProperty($event, 'parameter', $state->getName());

                            $issue->setState($state);

                            if ($state->isFinal()) {
                                $this->setProperty($issue, 'closedAt', $timestamp);
                            }

                            $transition = new Transition($event, $state);

                            $manager->persist($transition);

                            break;

                        case EventType::ISSUE_ASSIGNED:

                            /** @var \App\Entity\User $user */
                            $user = $this->getReference($row[self::EVENT_PARAMETER][$pref]);
                            $this->setProperty($event, 'parameter', $user->getFullname());

                            break;

                        case EventType::DEPENDENCY_ADDED:

                            /** @var \App\Entity\Issue $issue2 */
                            $issue2 = $this->getReference(sprintf($row[self::EVENT_PARAMETER], $pref));
                            $this->setProperty($event, 'parameter', $issue2->getFullId());

                            $dependency = new Dependency($event, $issue2);

                            $manager->persist($dependency);

                            break;

                        case EventType::DEPENDENCY_REMOVED:

                            /** @var \App\Entity\Issue $issue2 */
                            $issue2 = $this->getReference(sprintf($row[self::EVENT_PARAMETER], $pref));
                            $this->setProperty($event, 'parameter', $issue2->getFullId());

                            break;

                        default:

                            $this->setProperty($event, 'parameter', $row[self::EVENT_PARAMETER]);
                    }

                    $manager->persist($event);
                }

                $manager->persist($issue);
            }
        }

        $manager->flush();
    }
}
