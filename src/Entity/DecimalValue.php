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

use App\Repository\DecimalValueRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Decimal value.
 *
 * @ORM\Entity(repositoryClass=DecimalValueRepository::class)
 * @ORM\Table(name="decimal_values")
 */
class DecimalValue
{
    // Constraints.
    public const MIN_VALUE = '-9999999999.9999999999';
    public const MAX_VALUE = '9999999999.9999999999';
    public const PRECISION = 10;

    /**
     * Unique ID.
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected int $id;

    /**
     * Decimal value.
     *
     * @ORM\Column(name="value", type="decimal", precision=20, scale=10, unique=true)
     */
    protected string $value;

    /**
     * Creates new decimal value.
     *
     * @param string $value String representation of the value
     */
    public function __construct(string $value)
    {
        $this->value = $this->trim($value);
    }

    /**
     * Property getter.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Property getter.
     */
    public function getValue(): string
    {
        return $this->trim($this->value);
    }

    /**
     * Trims leading and trailing extra zeros from the specified number.
     */
    protected function trim(string $value): string
    {
        $value = mb_strpos($value, '.') === false
            ? ltrim($value, '0')
            : trim($value, '0');

        if (mb_strlen($value) === 0) {
            $value = '0';
        }
        elseif ($value[0] === '.') {
            $value = '0' . $value;
        }

        return rtrim($value, '.');
    }
}
