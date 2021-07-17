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

use App\Dictionary\FieldPermission;
use Doctrine\ORM\Mapping as ORM;

/**
 * Field permission for group.
 *
 * @ORM\Entity
 * @ORM\Table(name="field_group_permissions")
 */
class FieldGroupPermission
{
    /**
     * Field.
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=Field::class, inversedBy="groupPermissions")
     * @ORM\JoinColumn(name="field_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected Field $field;

    /**
     * Group.
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=Group::class)
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected Group $group;

    /**
     * Permission granted to the group for this field.
     *
     * @ORM\Id
     * @ORM\Column(name="permission", type="string", length=10)
     */
    protected string $permission;

    /**
     * Constructor.
     */
    public function __construct(Field $field, Group $group, string $permission)
    {
        if (!$group->isGlobal() && $group->getProject() !== $field->getState()->getTemplate()->getProject()) {
            throw new \UnexpectedValueException('Unknown group: ' . $group->getName());
        }

        if (!FieldPermission::has($permission)) {
            throw new \UnexpectedValueException('Unknown permission: ' . $permission);
        }

        $this->field      = $field;
        $this->group      = $group;
        $this->permission = $permission;
    }

    /**
     * Property getter.
     */
    public function getField(): Field
    {
        return $this->field;
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
