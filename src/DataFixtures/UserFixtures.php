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

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Test fixtures for 'User' entity.
 */
class UserFixtures extends Fixture implements FixtureInterface, DependentFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function getDependencies(): array
    {
        return [
            ProductionFixtures::class,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager): void
    {
        $data = [

            'artem@example.com' => [
                'fullname' => 'Artem Rodygin',
            ],
        ];

        foreach ($data as $email => $row) {

            $user = new User();

            $user
                ->setEmail($email)
                ->setPassword('secret')
                ->setFullname($row['fullname'])
                ->setDescription($row['description'] ?? null)
            ;

            $this->addReference('user:' . $email, $user);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
