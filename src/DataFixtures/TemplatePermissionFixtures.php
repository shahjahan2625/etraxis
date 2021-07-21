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

use App\Dictionary\SystemRole;
use App\Dictionary\TemplatePermission;
use App\Entity\TemplateGroupPermission;
use App\Entity\TemplateRolePermission;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Test fixtures for 'Template' entity.
 */
class TemplatePermissionFixtures extends Fixture implements FixtureInterface, DependentFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function getDependencies(): array
    {
        return [
            GroupFixtures::class,
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

                SystemRole::AUTHOR => [
                    TemplatePermission::CREATE_ISSUES,
                    TemplatePermission::EDIT_ISSUES,
                    TemplatePermission::REASSIGN_ISSUES,
                    TemplatePermission::SUSPEND_ISSUES,
                    TemplatePermission::RESUME_ISSUES,
                    TemplatePermission::ADD_COMMENTS,
                    TemplatePermission::PRIVATE_COMMENTS,
                    TemplatePermission::ATTACH_FILES,
                    TemplatePermission::DELETE_FILES,
                    TemplatePermission::ADD_DEPENDENCIES,
                    TemplatePermission::REMOVE_DEPENDENCIES,
                    TemplatePermission::SEND_REMINDERS,
                    TemplatePermission::DELETE_ISSUES,
                ],

                SystemRole::RESPONSIBLE => [
                    TemplatePermission::CREATE_ISSUES,
                    TemplatePermission::EDIT_ISSUES,
                    TemplatePermission::REASSIGN_ISSUES,
                    TemplatePermission::SUSPEND_ISSUES,
                    TemplatePermission::RESUME_ISSUES,
                    TemplatePermission::ADD_COMMENTS,
                    TemplatePermission::PRIVATE_COMMENTS,
                    TemplatePermission::ATTACH_FILES,
                    TemplatePermission::DELETE_FILES,
                    TemplatePermission::ADD_DEPENDENCIES,
                    TemplatePermission::REMOVE_DEPENDENCIES,
                    TemplatePermission::SEND_REMINDERS,
                    TemplatePermission::DELETE_ISSUES,
                ],

                'managers:%s' => [
                    TemplatePermission::VIEW_ISSUES,
                    TemplatePermission::CREATE_ISSUES,
                    TemplatePermission::EDIT_ISSUES,
                    TemplatePermission::REASSIGN_ISSUES,
                    TemplatePermission::SUSPEND_ISSUES,
                    TemplatePermission::RESUME_ISSUES,
                    TemplatePermission::ADD_COMMENTS,
                    TemplatePermission::PRIVATE_COMMENTS,
                    TemplatePermission::ATTACH_FILES,
                    TemplatePermission::DELETE_FILES,
                    TemplatePermission::ADD_DEPENDENCIES,
                    TemplatePermission::REMOVE_DEPENDENCIES,
                    TemplatePermission::SEND_REMINDERS,
                    TemplatePermission::DELETE_ISSUES,
                ],

                'developers:%s' => [
                    TemplatePermission::VIEW_ISSUES,
                    TemplatePermission::CREATE_ISSUES,
                ],

                'support:%s' => [
                    TemplatePermission::CREATE_ISSUES,
                ],
            ],

            'req' => [

                SystemRole::AUTHOR => [
                    TemplatePermission::EDIT_ISSUES,
                    TemplatePermission::ADD_COMMENTS,
                    TemplatePermission::ATTACH_FILES,
                    TemplatePermission::ADD_DEPENDENCIES,
                    TemplatePermission::REMOVE_DEPENDENCIES,
                ],

                SystemRole::RESPONSIBLE => [
                    TemplatePermission::ADD_COMMENTS,
                    TemplatePermission::ATTACH_FILES,
                    TemplatePermission::ADD_DEPENDENCIES,
                    TemplatePermission::REMOVE_DEPENDENCIES,
                ],

                'managers:%s' => [
                    TemplatePermission::VIEW_ISSUES,
                    TemplatePermission::CREATE_ISSUES,
                    TemplatePermission::EDIT_ISSUES,
                    TemplatePermission::REASSIGN_ISSUES,
                    TemplatePermission::SUSPEND_ISSUES,
                    TemplatePermission::RESUME_ISSUES,
                    TemplatePermission::ADD_COMMENTS,
                    TemplatePermission::PRIVATE_COMMENTS,
                    TemplatePermission::ATTACH_FILES,
                    TemplatePermission::DELETE_FILES,
                    TemplatePermission::ADD_DEPENDENCIES,
                    TemplatePermission::REMOVE_DEPENDENCIES,
                    TemplatePermission::SEND_REMINDERS,
                    TemplatePermission::DELETE_ISSUES,
                ],

                'clients:%s' => [
                    TemplatePermission::CREATE_ISSUES,
                ],

                'support:%s' => [
                    TemplatePermission::VIEW_ISSUES,
                    TemplatePermission::PRIVATE_COMMENTS,
                ],

                'staff' => [
                    TemplatePermission::VIEW_ISSUES,
                ],
            ],
        ];

        foreach (['a', 'b', 'c', 'd'] as $pref) {

            foreach ($data as $tref => $groups) {

                /** @var \App\Entity\Template $template */
                $template = $this->getReference(sprintf('%s:%s', $tref, $pref));

                foreach ($groups as $gref => $permissions) {

                    if (SystemRole::has($gref)) {
                        foreach ($permissions as $permission) {
                            $rolePermission = new TemplateRolePermission($template, $gref, $permission);
                            $manager->persist($rolePermission);
                        }
                    }
                    else {
                        /** @var \App\Entity\Group $group */
                        $group = $this->getReference(sprintf($gref, $pref));

                        foreach ($permissions as $permission) {
                            $groupPermission = new TemplateGroupPermission($template, $group, $permission);
                            $manager->persist($groupPermission);
                        }
                    }
                }
            }
        }

        $manager->flush();
    }
}
