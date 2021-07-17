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
use App\Dictionary\SystemRole;
use Doctrine\ORM\Mapping as ORM;

/**
 * Field permission for system role.
 *
 * @ORM\Entity
 * @ORM\Table(name="field_role_permissions")
 */
class FieldRolePermission
{
    /**
     * Field.
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=Field::class, inversedBy="rolePermissions")
     * @ORM\JoinColumn(name="field_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected Field $field;

    /**
     * System role.
     *
     * @ORM\Id
     * @ORM\Column(name="role", type="string", length=20)
     */
    protected string $role;

    /**
     * Permission granted to the role for this field.
     *
     * @ORM\Id
     * @ORM\Column(name="permission", type="string", length=10)
     */
    protected string $permission;

    /**
     * Constructor.
     */
    public function __construct(Field $field, string $role, string $permission)
    {
        if (!SystemRole::has($role)) {
            throw new \UnexpectedValueException('Unknown system role: ' . $role);
        }

        if (!FieldPermission::has($permission)) {
            throw new \UnexpectedValueException('Unknown permission: ' . $permission);
        }

        $this->field      = $field;
        $this->role       = $role;
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
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * Property getter.
     */
    public function getPermission(): string
    {
        return $this->permission;
    }
}
