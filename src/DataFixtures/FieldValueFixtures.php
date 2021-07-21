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
use App\Entity\DecimalValue;
use App\Entity\Event;
use App\Entity\FieldValue;
use App\Entity\ListItem;
use App\Entity\StringValue;
use App\Entity\TextValue;
use App\Entity\Transition;
use App\ReflectionTrait;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Test fixtures for 'FieldValue' entity.
 */
class FieldValueFixtures extends Fixture implements FixtureInterface, DependentFixtureInterface
{
    use ReflectionTrait;

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
                EventType::ISSUE_CREATED => [
                    'new:%s:priority'    => 2,
                    'new:%s:description' => 'Quas sunt reprehenderit vero accusantium.',
                    'new:%s:error'       => false,
                ],
                EventType::STATE_CHANGED => [
                    'assigned:%s:due date' => null,
                ],
                EventType::ISSUE_CLOSED  => [
                    'completed:%s:commit id'     => null,
                    'completed:%s:delta'         => 5173,
                    'completed:%s:effort'        => 1440,       // 24 hours
                    'completed:%s:test coverage' => '98.49',
                ],
            ],

            'task:%s:2' => [
                EventType::ISSUE_CREATED  => [
                    'new:%s:priority'    => 1,
                    'new:%s:description' => 'Velit voluptatem rerum nulla quos soluta excepturi omnis.',
                    'new:%s:error'       => true,
                ],
                EventType::STATE_CHANGED  => [
                    'assigned:%s:due date' => 7,
                ],
                EventType::ISSUE_CLOSED   => [
                    'completed:%s:commit id'     => '940059027173b8e8e1e3e874681f012f1f3bcf1d',
                    'completed:%s:delta'         => 1,
                    'completed:%s:effort'        => 80,         // 1:20
                    'completed:%s:test coverage' => null,
                ],
                EventType::ISSUE_REOPENED => [
                    'new:%s:new feature' => false,
                ],
            ],

            'task:%s:3' => [
                EventType::ISSUE_CREATED => [
                    'new:%s:priority'    => 2,
                    'new:%s:description' => 'Et nostrum et ut in ullam voluptatem dolorem et.',
                    'new:%s:new feature' => true,
                ],
                EventType::STATE_CHANGED => [
                    'assigned:%s:due date' => null,
                ],
                EventType::ISSUE_CLOSED  => [
                    'completed:%s:commit id'     => '067d9eebe965d2451cd3bd9333e46f38f3ec94c7',
                    'completed:%s:delta'         => 7403,
                    'completed:%s:effort'        => 2250,       // 37:30
                    'completed:%s:test coverage' => '99.05',
                ],
            ],

            'task:%s:4' => [
                EventType::ISSUE_CREATED => [
                    'new:%s:priority'    => 2,
                    'new:%s:description' => 'Omnis id quos recusandae provident.',
                    'new:%s:new feature' => true,
                ],
                EventType::ISSUE_CLOSED  => [
                    'duplicated:%s:task id' => 'task:%s:3',
                ],
            ],

            'task:%s:5' => [
                EventType::ISSUE_CREATED => [
                    'new:%s:priority'    => 2,
                    'new:%s:description' => null,
                    'new:%s:new feature' => false,
                ],
            ],

            'task:%s:6' => [
                EventType::ISSUE_CREATED => [
                    'new:%s:priority'    => 1,
                    'new:%s:description' => 'Voluptatum qui ratione sed molestias quo aliquam.',
                    'new:%s:new feature' => true,
                ],
            ],

            'task:%s:7' => [
                EventType::ISSUE_CREATED => [
                    'new:%s:priority'    => 2,
                    'new:%s:description' => 'Sapiente et velit aut minus sequi et.',
                    'new:%s:new feature' => true,
                ],
                EventType::STATE_CHANGED => [
                    'assigned:%s:due date' => 15,     // 1 day after creation + 14 days due
                ],
                EventType::ISSUE_CLOSED  => [
                    'duplicated:%s:issue id' => 'task:%s:6',
                ],
            ],

            'task:%s:8' => [
                EventType::ISSUE_CREATED => [
                    'new:%s:priority'    => 1,
                    'new:%s:description' => 'Esse labore et ducimus consequuntur labore voluptatem atque.',
                    'new:%s:new feature' => false,
                ],
                EventType::STATE_CHANGED => [
                    'assigned:%s:due date' => 6,        // 3 days after creation + 3 days due
                ],
            ],

            'req:%s:1' => [
                EventType::ISSUE_CREATED => [
                    'submitted:%s:details' => 'Expedita ullam iste omnis natus veritatis sint temporibus provident velit veniam provident rerum doloremque autem repellat est in sed.',
                ],
            ],

            'req:%s:2' => [
                EventType::ISSUE_CREATED => [
                    'submitted:%s:details' => 'Laborum sed saepe esse distinctio inventore nulla ipsam qui est qui laborum iste iure natus ea saepe qui recusandae similique est quia sed.',
                ],
            ],

            'req:%s:3' => [
                EventType::ISSUE_CREATED => [
                    'submitted:%s:details' => 'Est ut inventore omnis doloribus et corporis adipisci ut est rem sapiente numquam dolor voluptatibus quibusdam quo voluptates ab doloribus illum recusandae libero accusantium. Animi rem ut ut aperiam laborum sapiente quis dicta qui nostrum occaecati commodi non.',
                ],
            ],

            'req:%s:4' => [
                EventType::ISSUE_CREATED => [
                    'submitted:%s:details' => 'Distinctio maiores placeat quo cupiditate est autem excepturi cumque et dolorum qui rem minima ab enim dolor voluptas odio fugiat ea aspernatur voluptas enim. Sint dolor asperiores et facilis excepturi quasi perspiciatis ut ut reprehenderit aspernatur repellat adipisci ut aut laudantium cumque dicta ea non.',
                ],
            ],

            'req:%s:5' => [
                EventType::ISSUE_CREATED => [
                    'submitted:%s:details' => 'Sapiente cum placeat consequatur repellat est aliquid ut sed praesentium aliquid dolorum cumque quas qui maiores consequatur nihil commodi iure architecto molestias libero. Dicta id illum officiis ut numquam et et quisquam libero voluptatem ad accusamus aspernatur est consequatur et minima reiciendis repellat culpa.',
                ],
            ],

            'req:%s:6' => [
                EventType::ISSUE_CREATED => [
                    'submitted:%s:details' => 'Quis quaerat ut corrupti vitae sed rerum voluptate consequatur odio molestiae voluptatibus esse nostrum sunt perspiciatis in fuga est vitae enim. Voluptas distinctio enim ullam iusto voluptate vitae voluptatem ipsa placeat asperiores molestiae eveniet expedita at officiis incidunt amet.',
                ],
            ],
        ];

        foreach (['a', 'b', 'c'] as $pref) {

            foreach ($data as $iref => $event_types) {

                foreach ($event_types as $event_type => $fields) {

                    /** @var \App\Entity\Issue $issue */
                    $issue = $this->getReference(sprintf($iref, $pref));

                    /** @var Event $event */
                    [$event] = $manager->getRepository(Event::class)->findBy([
                        'type'  => $event_type,
                        'issue' => $issue,
                    ]);

                    /** @var Transition $transition */
                    $transition = $manager->getRepository(Transition::class)->findOneBy(['event' => $event]);

                    foreach ($fields as $fref => $vref) {

                        /** @var \App\Entity\Field $field */
                        $field = $this->getReference(sprintf($fref, $pref));

                        $value = $vref;

                        if ($value !== null) {

                            switch ($field->getType()) {

                                case FieldType::CHECKBOX:

                                    $value = $vref ? 1 : 0;

                                    break;

                                case FieldType::DATE:

                                    $value = $issue->getCreatedAt() + $vref * self::ONE_DAY;

                                    break;

                                case FieldType::DECIMAL:

                                    /** @var \App\Repository\Contracts\DecimalValueRepositoryInterface $repository */
                                    $repository = $manager->getRepository(DecimalValue::class);
                                    $value      = $repository->get($vref)->getId();

                                    break;

                                case FieldType::DURATION:

                                    $value = $vref;

                                    break;

                                case FieldType::ISSUE:

                                    /** @var \App\Entity\Issue $entity */
                                    $entity = $this->getReference(sprintf($vref, $pref));
                                    $value  = $entity->getId();

                                    break;

                                case FieldType::LIST:

                                    /** @var \App\Repository\Contracts\ListItemRepositoryInterface $repository */
                                    $repository = $manager->getRepository(ListItem::class);
                                    $value      = $repository->findOneBy(['field' => $field, 'value' => $vref])->getId();

                                    break;

                                case FieldType::NUMBER:

                                    break;

                                case FieldType::STRING:

                                    /** @var \App\Repository\Contracts\StringValueRepositoryInterface $repository */
                                    $repository = $manager->getRepository(StringValue::class);
                                    $value      = $repository->get($vref)->getId();

                                    break;

                                case FieldType::TEXT:

                                    /** @var \App\Repository\Contracts\TextValueRepositoryInterface $repository */
                                    $repository = $manager->getRepository(TextValue::class);
                                    $value      = $repository->get($vref)->getId();

                                    break;
                            }
                        }

                        $fieldValue = new FieldValue($transition, $field, $value);

                        $this->setProperty($fieldValue, 'createdAt', $event->getCreatedAt());

                        $manager->persist($fieldValue);
                    }

                    $manager->persist($issue);
                }
            }
        }

        $manager->flush();
    }
}
