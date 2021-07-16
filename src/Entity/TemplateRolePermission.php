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

use App\Dictionary\SystemRole;
use App\Dictionary\TemplatePermission;
use Doctrine\ORM\Mapping as ORM;

/**
 * Template permission for system role.
 *
 * @ORM\Entity
 * @ORM\Table(name="template_role_permissions")
 */
class TemplateRolePermission
{
    /**
     * Template.
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=Template::class, inversedBy="rolePermissions")
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected Template $template;

    /**
     * System role.
     *
     * @ORM\Id
     * @ORM\Column(name="role", type="string", length=20)
     */
    protected string $role;

    /**
     * Permission granted to the role for this template.
     *
     * @ORM\Id
     * @ORM\Column(name="permission", type="string", length=20)
     */
    protected string $permission;

    /**
     * Constructor.
     */
    public function __construct(Template $template, string $role, string $permission)
    {
        if (!SystemRole::has($role)) {
            throw new \UnexpectedValueException('Unknown system role: ' . $role);
        }

        if (!TemplatePermission::has($permission)) {
            throw new \UnexpectedValueException('Unknown permission: ' . $permission);
        }

        $this->template   = $template;
        $this->role       = $role;
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
