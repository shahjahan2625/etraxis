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

use App\Dictionary\FieldType;
use App\Entity\DecimalValue;
use App\Entity\Field;
use App\Entity\FieldValue;
use App\Entity\StringValue;
use App\Entity\TextValue;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Test fixtures for 'Field' entity.
 */
class FieldFixtures extends Fixture implements FixtureInterface, DependentFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function getDependencies(): array
    {
        return [
            StateFixtures::class,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager): void
    {
        $data = [

            'new' => [
                [
                    'type'     => FieldType::LIST,
                    'name'     => 'Priority',
                    'required' => true,
                    'position' => 1,
                ],
                [
                    'type'       => FieldType::TEXT,
                    'name'       => 'Description',
                    'required'   => false,
                    'position'   => 2,
                    'parameters' => function (Field $field) use ($manager): void {
                        /** @var \App\Repository\Contracts\TextValueRepositoryInterface $repository */
                        $repository = $manager->getRepository(TextValue::class);
                        $default    = $repository->get('How to reproduce:');

                        $field
                            ->setParameter(Field::LENGTH, TextValue::MAX_VALUE)
                            ->setParameter(Field::DEFAULT, $default->getId())
                        ;
                    },
                ],
                [
                    'type'     => FieldType::CHECKBOX,
                    'name'     => 'Error',
                    'required' => false,
                    'position' => 3,
                    'deleted'  => true,
                ],
                [
                    'type'     => FieldType::CHECKBOX,
                    'name'     => 'New feature',
                    'required' => false,
                    'position' => 3,
                ],
            ],

            'assigned' => [
                [
                    'type'       => FieldType::DATE,
                    'name'       => 'Due date',
                    'required'   => false,
                    'position'   => 1,
                    'parameters' => function (Field $field): void {
                        $field
                            ->setParameter(Field::MINIMUM, 0)
                            ->setParameter(Field::MAXIMUM, 14)
                            ->setParameter(Field::DEFAULT, 14)
                        ;
                    },
                ],
            ],

            'completed' => [
                [
                    'type'       => FieldType::STRING,
                    'name'       => 'Commit ID',
                    'required'   => false,
                    'position'   => 1,
                    'parameters' => function (Field $field) use ($manager): void {
                        /** @var \App\Repository\Contracts\StringValueRepositoryInterface $repository */
                        $repository = $manager->getRepository(StringValue::class);
                        $default    = $repository->get('Git commit ID');

                        $field
                            ->setParameter(Field::LENGTH, 40)
                            ->setParameter(Field::DEFAULT, $default->getId())
                        ;
                    },
                ],
                [
                    'type'        => FieldType::NUMBER,
                    'name'        => 'Delta',
                    'description' => 'NCLOC',
                    'required'    => true,
                    'position'    => 2,
                    'parameters'  => function (Field $field): void {
                        $field
                            ->setParameter(Field::MINIMUM, 0)
                            ->setParameter(Field::MAXIMUM, FieldValue::MAX_NUMBER_VALUE)
                        ;
                    },
                ],
                [
                    'type'        => FieldType::DURATION,
                    'name'        => 'Effort',
                    'description' => 'HH:MM',
                    'required'    => true,
                    'position'    => 3,
                    'parameters'  => function (Field $field): void {
                        $field
                            ->setParameter(Field::MINIMUM, FieldValue::MIN_DURATION_VALUE)
                            ->setParameter(Field::MAXIMUM, FieldValue::MAX_DURATION_VALUE)
                        ;
                    },
                ],
                [
                    'type'       => FieldType::DECIMAL,
                    'name'       => 'Test coverage',
                    'required'   => false,
                    'position'   => 4,
                    'parameters' => function (Field $field) use ($manager): void {
                        /** @var \App\Repository\Contracts\DecimalValueRepositoryInterface $repository */
                        $repository = $manager->getRepository(DecimalValue::class);
                        $minimum    = $repository->get('0');
                        $maximum    = $repository->get('100');

                        $field
                            ->setParameter(Field::MINIMUM, $minimum->getId())
                            ->setParameter(Field::MAXIMUM, $maximum->getId())
                        ;
                    },
                ],
            ],

            'duplicated' => [
                [
                    'type'     => FieldType::ISSUE,
                    'name'     => 'Task ID',
                    'required' => true,
                    'position' => 1,
                    'deleted'  => true,
                ],
                [
                    'type'     => FieldType::ISSUE,
                    'name'     => 'Issue ID',
                    'required' => true,
                    'position' => 1,
                ],
            ],

            'submitted' => [
                [
                    'type'       => FieldType::TEXT,
                    'name'       => 'Details',
                    'required'   => true,
                    'position'   => 1,
                    'parameters' => function (Field $field): void {
                        $field->setParameter(Field::LENGTH, 250);
                    },
                ],
            ],

            'opened' => [],

            'resolved' => [],
        ];

        foreach (['a', 'b', 'c', 'd'] as $pref) {

            foreach ($data as $sref => $fields) {

                /** @var \App\Entity\State $state */
                $state = $this->getReference(sprintf('%s:%s', $sref, $pref));

                foreach ($fields as $row) {

                    $field = new Field($state, $row['type']);

                    $field
                        ->setName($row['name'])
                        ->setDescription($row['description'] ?? null)
                        ->setPosition($row['position'])
                        ->setRequired($row['required'])
                    ;

                    if ($row['parameters'] ?? false) {
                        $row['parameters']($field);
                    }

                    if ($row['deleted'] ?? false) {
                        $field->remove();
                    }

                    $this->addReference(sprintf('%s:%s:%s', $sref, $pref, mb_strtolower($row['name'])), $field);

                    $manager->persist($field);
                }
            }
        }

        $manager->flush();
    }
}
