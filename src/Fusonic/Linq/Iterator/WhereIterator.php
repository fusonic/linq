<?php

/*
 * This file is part of Fusonic-linq.
 * https://github.com/fusonic/fusonic-linq
 *
 * (c) Fusonic GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fusonic\Linq\Iterator;

use Iterator;
use Fusonic\Linq\Helper;

class WhereIterator extends \FilterIterator
{
    private $func;

    public function __construct(Iterator $iterator, $func)
    {
        parent::__construct($iterator);
        $this->func = $func;
    }

    public function accept()
    {
        $func = $this->func;
        $current = $this->current();
        return Helper\LinqHelper::getBoolOrThrowException($func($current));
    }
}