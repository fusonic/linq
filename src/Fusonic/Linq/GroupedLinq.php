<?php

/*
 * This file is part of Fusonic-linq.
 *
 * (c) Fusonic GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fusonic\Linq;

use Fusonic\Linq\Linq;

/**
 * Class GroupedLinq
 * Represents a Linq object that groups together other elements with a group key().
 * @package Fusonic\Linq
 */
class GroupedLinq extends Linq
{
    private $groupKey;

    public function __construct($groupKey, $dataSource)
    {
        parent::__construct($dataSource);
        $this->groupKey = $groupKey;
    }

    public function key()
    {
        return $this->groupKey;
    }
}