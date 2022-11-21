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

use Fusonic\Linq\Helper;
use IteratorAggregate;
use Traversable;

final class OfTypeIterator implements IteratorAggregate
{
	private Traversable $iterator;
	/** @var callable */
	private $acceptCallback;

	public function __construct(Traversable $iterator, string $type)
	{
		$this->iterator = $iterator;

		switch (strtolower($type)) {
			case 'int':
			case 'integer':
				$this->acceptCallback = function ($current) {
					return is_int($current);
				};
				break;
			case 'float':
			case 'double':
				$this->acceptCallback = function ($current) {
					return is_float($current);
				};
				break;
			case 'string':
				$this->acceptCallback = function ($current) {
					return is_string($current);
				};
				break;
			case 'bool':
			case 'boolean':
				$this->acceptCallback = function ($current) {
					return is_bool($current);
				};
				break;

			default:
				$this->acceptCallback = function ($current) use ($type) {
					return $current instanceof $type;
				};
		}
	}

	public function getIterator(): \Generator
	{
		$func = $this->acceptCallback;
		foreach($this->iterator as $current) {
			if($func($current)) {
				yield $current;
			}
		}
	}
}
