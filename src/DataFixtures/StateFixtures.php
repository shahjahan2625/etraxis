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

use App\Dictionary\StateResponsible;
use App\Dictionary\StateType;
use App\Entity\State;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Test fixtures for 'State' entity.
 */
class StateFixtures extends Fixture implements FixtureInterface, DependentFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function getDependencies(): array
    {
        return [
            TemplateFixtures::class,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager): void
    {
        $data = [

            'task' => [
                'Assigned'   => [
                    'type'        => StateType::INTERMEDIATE,
                    'responsible' => StateResponsible::ASSIGN,
                ],
                'New'        => [
                    'type'        => StateType::INITIAL,
                    'responsible' => StateResponsible::REMOVE,
                    'next'        => 'assigned',
                ],
                'Completed'  => [
                    'type' => StateType::FINAL,
                ],
                'Duplicated' => [
                    'type' => StateType::FINAL,
                ],
            ],

            'req' => [
                'Submitted' => [
                    'type'        => StateType::INITIAL,
                    'responsible' => StateResponsible::KEEP,
                ],
                'Opened'    => [
                    'type'        => StateType::INTERMEDIATE,
                    'responsible' => StateResponsible::ASSIGN,
                ],
                'Resolved'  => [
                    'type' => StateType::FINAL,
                ],
            ],
        ];

        foreach (['a', 'b', 'c', 'd'] as $pref) {

            foreach ($data as $tref => $states) {

                /** @var \App\Entity\Template $template */
                $template = $this->getReference(sprintf('%s:%s', $tref, $pref));

                foreach ($states as $name => $row) {

                    $state = new State($template, $pref === 'd' ? StateType::INTERMEDIATE : $row['type']);

                    $state
                        ->setName($name)
                        ->setResponsible($row['responsible'] ?? StateResponsible::REMOVE)
                    ;

                    if ($row['next'] ?? null) {
                        /** @var State $nextState */
                        $nextState = $this->getReference(sprintf('%s:%s', $row['next'], $pref));

                        $state->setNextState($nextState);
                    }

                    $this->addReference(sprintf('%s:%s', mb_strtolower($name), $pref), $state);

                    $manager->persist($state);
                }
            }
        }

        $manager->flush();
    }
}
