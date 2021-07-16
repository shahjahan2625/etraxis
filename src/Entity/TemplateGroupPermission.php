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

use App\Dictionary\TemplatePermission;
use Doctrine\ORM\Mapping as ORM;

/**
 * Template permission for group.
 *
 * @ORM\Entity
 * @ORM\Table(name="template_group_permissions")
 */
class TemplateGroupPermission
{
    /**
     * Template.
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=Template::class, inversedBy="groupPermissions")
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected Template $template;

    /**
     * Group.
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=Group::class)
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected Group $group;

    /**
     * Permission granted to the group for this template.
     *
     * @ORM\Id
     * @ORM\Column(name="permission", type="string", length=20)
     */
    protected string $permission;

    /**
     * Constructor.
     */
    public function __construct(Template $template, Group $group, string $permission)
    {
        if (!$group->isGlobal() && $group->getProject() !== $template->getProject()) {
            throw new \UnexpectedValueException('Unknown group: ' . $group->getName());
        }

        if (!TemplatePermission::has($permission)) {
            throw new \UnexpectedValueException('Unknown permission: ' . $permission);
        }

        $this->template   = $template;
        $this->group      = $group;
        $this->permission = $permission;
    }

    /**
     * Property getter.
     */
    public function getTemplate(): Template
    {
        return $this->template;
    }

    /**
     * Property getter.
     */
    public function getGroup(): Group
    {
        return $this->group;
    }

    /**
     * Property getter.
     */
    public function getPermission(): string
    {
        return $this->permission;
    }
}
