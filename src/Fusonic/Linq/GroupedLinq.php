<?php

namespace Fusonic\Linq;

use Fusonic\Linq\Linq;

/**
 * Class GroupedLinq
 * Represents a Linq object that groups together other elements with its groupKey()
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

    public function groupKey()
    {
        return $this->groupKey;
    }
}
