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
use App\Dictionary\FieldType;
use App\Entity\Change;
use App\Entity\Event;
use App\Entity\ListItem;
use App\Entity\StringValue;
use App\Entity\TextValue;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Test fixtures for 'Change' entity.
 */
class ChangeFixtures extends Fixture implements FixtureInterface, DependentFixtureInterface
{
    // Number of seconds in one day.
    protected const ONE_DAY = 86400;

    /**
     * {@inheritDoc}
     */
    public function getDependencies(): array
    {
        return [
            FieldFixtures::class,
            ListItemFixtures::class,
            IssueFixtures::class,
            EventFixtures::class,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager): void
    {
        $data = [

            'task:%s:1' => [
                [
                    'subject'         => ['Task 1', 'Development task 1'],
                    'new:%s:priority' => [3, 2],
                ],
            ],

            'task:%s:2' => [
                [
                    'new:%s:priority'    => [3, 1],
                    'new:%s:description' => [
                        'Velit voluptatem rerum nulla quos.',
                        'Velit voluptatem rerum nulla quos soluta excepturi omnis.',
                    ],
                ],
                [
                    'assigned:%s:due date' => [14, 7],
                ],
            ],
        ];

        foreach (['a', 'b', 'c'] as $pref) {

            foreach ($data as $iref => $events) {

                /** @var \App\Entity\Issue $issue */
                $issue = $this->getReference(sprintf($iref, $pref));
                $manager->refresh($issue);

                foreach ($events as $index => $row) {

                    /** @var Event[] $events */
                    $events = $manager->getRepository(Event::class)->findBy([
                        'type'  => EventType::ISSUE_EDITED,
                        'issue' => $issue,
                    ], [
                        'createdAt' => 'ASC',
                    ]);

                    $event = $events[$index];

                    foreach ($row as $fref => $values) {

                        $field    = null;
                        $oldValue = null;
                        $newValue = null;

                        if ($fref === 'subject') {

                            /** @var \App\Repository\Contracts\StringValueRepositoryInterface $repository */
                            $repository = $manager->getRepository(StringValue::class);

                            $oldValue = $repository->get($values[0])->getId();
                            $newValue = $repository->get($values[1])->getId();
                        }
                        else {

                            /** @var \App\Entity\Field $field */
                            $field = $this->getReference(sprintf($fref, $pref));

                            switch ($field->getType()) {

                                case FieldType::TEXT:

                                    /** @var \App\Repository\Contracts\TextValueRepositoryInterface $repository */
                                    $repository = $manager->getRepository(TextValue::class);

                                    $oldValue = $repository->get($values[0])->getId();
                                    $newValue = $repository->get($values[1])->getId();

                                    break;

                                case FieldType::LIST:

                                    /** @var \App\Repository\Contracts\ListItemRepositoryInterface $repository */
                                    $repository = $manager->getRepository(ListItem::class);

                                    $oldValue = $repository->findOneBy(['field' => $field, 'value' => $values[0]])->getId();
                                    $newValue = $repository->findOneBy(['field' => $field, 'value' => $values[1]])->getId();

                                    break;

                                case FieldType::DATE:

                                    $oldValue = $issue->getCreatedAt() + $values[0] * self::ONE_DAY;
                                    $newValue = $issue->getCreatedAt() + $values[1] * self::ONE_DAY;

                                    break;
                            }
                        }

                        $change = new Change($event, $field, $oldValue, $newValue);

                        $manager->persist($change);
                    }
                }
            }
        }

        $manager->flush();
    }
}
