<?php
/*
 * This file is part of Fusonic-linq.
 * https://github.com/fusonic/fusonic-linq
 *
 * (c) Burgy Benjamin <benjamin.burgy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fusonic\Linq\Iterator;

use FilterIterator;
use Fusonic\Linq\Helper;
use Iterator;
use Traversable;

/**
 * Iterator for filtering the Linq query with a specified <b>type</b>.
 * @package Fusonic\Linq\Iterator
 */
final class OfTypeIterator extends \FilterIterator
{
	/**
	 * @var callable $acceptCallback
	 */
	private $acceptCallback;

	/**
	 * Initializes an instance of <b>OfTypeIterator</b>.
	 *
	 * @param Iterator $iterator
	 * @param string   $type
	 */
	public function __construct(Traversable $iterator, $type)
	{
		parent::__construct($iterator);

		switch (strtolower($type))
		{
			case 'int':
			case 'integer':
				$this->acceptCallback = function ($current)
				{
					return is_int($current);
				};
				break;
			case 'float':
			case 'double':
				$this->acceptCallback = function ($current)
				{
					return is_float($current);
				};
				break;
			case 'string':
				$this->acceptCallback = function ($current)
				{
					return is_string($current);
				};
				break;
			case 'bool':
			case 'boolean':
				$this->acceptCallback = function ($current)
				{
					return is_bool($current);
				};
				break;

			default:
				$this->acceptCallback = function ($current) use ($type)
				{
					return $current instanceof $type;
				};
		}
	}

	public function accept()
	{
		$current = $this->current();
		$func = $this->acceptCallback;

		return $func($current);
	}
}